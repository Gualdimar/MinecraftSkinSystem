package net.minecraft;

import java.applet.Applet;
import java.io.DataInputStream;
import java.io.DataOutputStream;
import java.io.EOFException;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileOutputStream;
import java.io.FilePermission;
import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;
import java.io.PrintWriter;
import java.io.StringWriter;
import java.io.Writer;
import java.lang.reflect.Field;
import java.lang.reflect.Method;
import java.net.HttpURLConnection;
import java.net.JarURLConnection;
import java.net.SocketPermission;
import java.net.URL;
import java.net.URLClassLoader;
import java.net.URLConnection;
import java.security.AccessControlException;
import java.security.AccessController;
import java.security.CodeSource;
import java.security.MessageDigest;
import java.security.PermissionCollection;
import java.security.PrivilegedActionException;
import java.security.PrivilegedExceptionAction;
import java.security.SecureClassLoader;
import java.security.cert.Certificate;
import java.util.Enumeration;
import java.util.Properties;
import java.util.StringTokenizer;
import java.util.Vector;
import java.util.jar.JarEntry;
import java.util.jar.JarFile;
import java.util.jar.JarOutputStream;
import java.util.jar.Pack200;
import java.util.zip.ZipEntry;
import java.util.zip.ZipFile;

import SevenZip.LzmaAlone;

public class GameUpdater
  implements Runnable
{
  public static final int STATE_INIT = 1;
  public static final int STATE_DETERMINING_PACKAGES = 2;
  public static final int STATE_CHECKING_CACHE = 3;
  public static final int STATE_DOWNLOADING = 4;
  public static final int STATE_EXTRACTING_PACKAGES = 5;
  public static final int STATE_UPDATING_CLASSPATH = 6;
  public static final int STATE_SWITCHING_APPLET = 7;
  public static final int STATE_INITIALIZE_REAL_APPLET = 8;
  public static final int STATE_START_REAL_APPLET = 9;
  public static final int STATE_DONE = 10;
public int percentage;
  public int currentSizeDownload;
  public int totalSizeDownload;
  public int currentSizeExtract;
  public int totalSizeExtract;
  protected URL[] urlList;
  private static ClassLoader classLoader;
  protected Thread loaderThread;
  protected Thread animationThread;
  public boolean fatalError;
  public String fatalErrorDescription;
  protected String subtaskMessage = "";
  protected int state = 1;

  protected boolean lzmaSupported = false;
  protected boolean pack200Supported = false;

  protected String[] genericErrorMessage = { 
    "An error occured while loading the applet.", "Please contact support to resolve this issue.", "<placeholder for error message>" };
  protected boolean certificateRefused;
  protected String[] certificateRefusedMessage = { 
    "Permissions for Applet Refused.", "Please accept the permissions dialog to allow", "the applet to continue the loading process." };

  protected static boolean natives_loaded = false;
  public static boolean forceUpdate = false;
  private String latestVersion;
  private String mainGameUrl;
  public boolean pauseAskUpdate;
  public boolean shouldUpdate;
  
  public GameUpdater(String latestVersion, String mainGameUrl)
  {
    this.latestVersion = latestVersion;
    this.mainGameUrl = mainGameUrl;
  }

  public void init() {
    state = 1;
    try
    {
      Class.forName("LZMA.LzmaInputStream");
      lzmaSupported = true;
    }
    catch (Throwable localThrowable) {
    }
    try {
      Pack200.class.getSimpleName();
      pack200Supported = true;
    } catch (Throwable localThrowable1) {
    }
  }

  private String generateStacktrace(Exception exception) {
    Writer result = new StringWriter();
    PrintWriter printWriter = new PrintWriter(result);
    exception.printStackTrace(printWriter);
    return result.toString();
  }

  protected String getDescriptionForState()
  {
    switch (state) {
    case 1:
      return "Инициализация загрузчика";
    case 2:
      return "Обнаружение пакетов для скачки";
    case 3:
      return "Проверка кеш-файлов";
    case 4:
      return "Скачивание пакетов";
    case 5:
      return "Извлечение скачанных пакетов";
    case 6:
      return "Обновление путей";
    case 7:
      return "Сворачивание апплета";
    case 8:
      return "Инициализация реального апплета";
    case 9:
      return "Старт реального апплета";
    case 10:
      return "Загрузка завершена";
    }
    return "Неизвестное положение";
  }

  protected String trimExtensionByCapabilities(String file)
  {
    if (!pack200Supported) {
      file = file.replaceAll(".pack", "");
    }

    if (!lzmaSupported) {
      file = file.replaceAll(".lzma", "");
    }
    return file;
  }

  protected void loadJarURLs() throws Exception {
    state = 2;
    
    //#Внимание на сервере должен быть файл client.zip, даже пустой!
    String jarList = "lwjgl.jar, jinput.jar, lwjgl_util.jar, client.zip, " + mainGameUrl;
    jarList = trimExtensionByCapabilities(jarList);

    StringTokenizer jar = new StringTokenizer(jarList, ", ");
    int jarCount = jar.countTokens() + 1;

    urlList = new URL[jarCount];
    
    //# Откуда скачивать
    URL path = new URL("http://s3.amazonaws.com/MinecraftDownload/");

    for (int i = 0; i < jarCount - 1; i++) {
      urlList[i] = new URL(path, jar.nextToken());
    }

    String osName = System.getProperty("os.name");
    String nativeJar = null;

    if (osName.startsWith("Win"))
      nativeJar = "windows_natives.jar.lzma";
    else if (osName.startsWith("Linux"))
      nativeJar = "linux_natives.jar.lzma";
    else if (osName.startsWith("Mac"))
      nativeJar = "macosx_natives.jar.lzma";
    else if ((osName.startsWith("Solaris")) || (osName.startsWith("SunOS")))
      nativeJar = "solaris_natives.jar.lzma";
    else {
      fatalErrorOccured("OS (" + osName + ") не поддерживается", null);
    }

    if (nativeJar == null) {
      fatalErrorOccured("lwjgl файлы не найдены", null);
    } else {
      nativeJar = trimExtensionByCapabilities(nativeJar);
      urlList[(jarCount - 1)] = new URL(path, nativeJar);
    }
  }

  public void run()
  {
    init();
    state = 3;

    percentage = 5;
    try
    {
      loadJarURLs();

      String path = (String)AccessController.doPrivileged(new PrivilegedExceptionAction<Object>() {
        public Object run() throws Exception {
          return Util.getWorkingDirectory() + File.separator + "bin" + File.separator;
        }
      });
      File dir = new File(path);

      if (!dir.exists()) {
        dir.mkdirs();
      }
      
      if (latestVersion != null) {
        File versionFile = new File(dir, "version-mcss");
       
        boolean cacheAvailable = false;
        if ((!forceUpdate) && (versionFile.exists()) && (
          (latestVersion.equals("-1")) || (latestVersion.equals(readVersionFile(versionFile))))) {
          cacheAvailable = true;
          percentage = 90;
        }

        if ((forceUpdate) || (!cacheAvailable)) {
          shouldUpdate = true;
          if ((!forceUpdate) && (versionFile.exists()))
          {
            checkShouldUpdate();
          }
          if (shouldUpdate)
          {
            writeVersionFile(versionFile, "");

            downloadJars(path);
            extractJars(path);
            extractNatives(path);

            if (latestVersion != null) {
              percentage = 90;
              writeVersionFile(versionFile, latestVersion);
            }
          } else {
            cacheAvailable = true;
            percentage = 90;
          }
        }
      }

      updateClassPath(dir);
      state = 10;
    } catch (AccessControlException ace) {
      fatalErrorOccured(ace.getMessage(), ace);
      certificateRefused = true;
    } catch (Exception e) {
      fatalErrorOccured(e.getMessage(), e);
    } finally {
      loaderThread = null;
    }
  }



private void checkShouldUpdate() {
    pauseAskUpdate = true;
    while (pauseAskUpdate)
      try {
        Thread.sleep(1000L);
      } catch (InterruptedException e) {
        e.printStackTrace();
      }
  }

  protected String readVersionFile(File file) throws Exception
  {
    DataInputStream dis = new DataInputStream(new FileInputStream(file));
    String version = dis.readUTF();
    dis.close();
    return version;
  }

  protected void writeVersionFile(File file, String version) throws Exception {
    DataOutputStream dos = new DataOutputStream(new FileOutputStream(file));
    dos.writeUTF(version);
    dos.close();
  }

  protected void updateClassPath(File dir)
    throws Exception
  {
    state = 6;

    percentage = 95;

    URL[] urls = new URL[urlList.length];
    for (int i = 0; i < urlList.length; i++) {
      urls[i] = new File(dir, getJarName(urlList[i])).toURI().toURL();
    }

    if (classLoader == null) {
      classLoader = new URLClassLoader(urls) {
        protected PermissionCollection getPermissions(CodeSource codesource) {
          PermissionCollection perms = null;
          try
          {
            Method method = SecureClassLoader.class.getDeclaredMethod("getPermissions", new Class[] { 
              CodeSource.class });

            method.setAccessible(true);
            perms = (PermissionCollection)method.invoke(getClass().getClassLoader(), new Object[] { 
              codesource });

            String host = "www.minecraft.net";

            if ((host != null) && (host.length() > 0))
            {
              perms.add(new SocketPermission(host, "connect,accept"));
            } else codesource.getLocation().getProtocol().equals("file");

            perms.add(new FilePermission("<<ALL FILES>>", "read"));
          }
          catch (Exception e) {
            e.printStackTrace();
          }

          return perms;
        }
      };
    }
    String path = dir.getAbsolutePath();
    if (!path.endsWith(File.separator)) path = path + File.separator;
    unloadNatives(path);

    System.setProperty("org.lwjgl.librarypath", path + "natives");
    System.setProperty("net.java.games.input.librarypath", path + "natives");

    natives_loaded = true;
  }

  private void unloadNatives(String nativePath)
  {
    if (!natives_loaded) {
      return;
    }
    try
    {
      Field field = ClassLoader.class.getDeclaredField("loadedLibraryNames");
      field.setAccessible(true);
      Vector<?> libs = (Vector<?>)field.get(getClass().getClassLoader());

      String path = new File(nativePath).getCanonicalPath();

      for (int i = 0; i < libs.size(); i++) {
        String s = (String)libs.get(i);

        if (s.startsWith(path)) {
          libs.remove(i);
          i--;
        }
      }
    } catch (Exception e) {
      e.printStackTrace();
    }
  }

  public Applet createApplet() throws ClassNotFoundException, InstantiationException, IllegalAccessException
  {
    Class<?> appletClass = classLoader.loadClass("net.minecraft.client.MinecraftApplet");
    return (Applet)appletClass.newInstance();
  }  
  
  protected void downloadJars(String path)
    throws Exception
  {
    File versionFile = new File(path, "md5s");
    Properties md5s = new Properties();
    if (versionFile.exists()) {
      try {
        FileInputStream fis = new FileInputStream(versionFile);
        md5s.load(fis);
        fis.close();
      } catch (Exception e) {
        e.printStackTrace();
      }
    }
    state = 4;

    int[] fileSizes = new int[urlList.length];
    boolean[] skip = new boolean[urlList.length];

    for (int i = 0; i < urlList.length; i++) {
      URLConnection urlconnection = urlList[i].openConnection();
      urlconnection.setDefaultUseCaches(false);
      skip[i] = false;
      if ((urlconnection instanceof HttpURLConnection)) {
        ((HttpURLConnection)urlconnection).setRequestMethod("HEAD");

        int code = ((HttpURLConnection)urlconnection).getResponseCode();
        if (code / 100 == 3) {
          skip[i] = true;
        }
      }
      fileSizes[i] = urlconnection.getContentLength();
      totalSizeDownload += fileSizes[i];
    }

    int initialPercentage = this.percentage = 10;

    byte[] buffer = new byte[65536];
    for (int i = 0; i < urlList.length; i++) {
      if (skip[i] != false) {
        percentage = (initialPercentage + fileSizes[i] * 45 / totalSizeDownload);
      }

        boolean downloadFile = true;

        while (downloadFile) {
          downloadFile = false;

          URLConnection urlconnection = urlList[i].openConnection();

          String etag = "";

          if ((urlconnection instanceof HttpURLConnection)) {
            urlconnection.setRequestProperty("Cache-Control", "no-cache");

            urlconnection.connect();

            etag = urlconnection.getHeaderField("ETag");
          }

          String currentFile = getFileName(urlList[i]);
          InputStream inputstream = getJarInputStream(currentFile, urlconnection);
          
          FileOutputStream fos = new FileOutputStream(path + currentFile);

          long downloadStartTime = System.currentTimeMillis();
          int downloadedAmount = 0;
          int fileSize = 0;
          String downloadSpeedMessage = "";

          MessageDigest m = MessageDigest.getInstance("MD5");
          int bufferSize;
          while ((bufferSize = inputstream.read(buffer, 0, buffer.length)) != -1)
          {

            fos.write(buffer, 0, bufferSize);

            m.update(buffer, 0, bufferSize);
            currentSizeDownload += bufferSize;
            fileSize += bufferSize;
            percentage = (initialPercentage + currentSizeDownload * 45 / totalSizeDownload);
            subtaskMessage = ("Загрузка: " + currentFile + " " + currentSizeDownload * 100 / totalSizeDownload + "%");

            downloadedAmount += bufferSize;
            long timeLapse = System.currentTimeMillis() - downloadStartTime;

            if (timeLapse >= 1000L) {
              float downloadSpeed = downloadedAmount / (float)timeLapse;
              downloadSpeed = (int)(downloadSpeed * 100.0F) / 100.0F;
              downloadSpeedMessage = " @ " + downloadSpeed + " KB/sec";
              downloadedAmount = 0;
              downloadStartTime += 1000L;
            }

            subtaskMessage += downloadSpeedMessage;
          }

          inputstream.close();
          fos.close();

          }
          }
    }

  protected InputStream getJarInputStream(String currentFile, final URLConnection urlconnection)
    throws Exception
  {
    final InputStream[] is = new InputStream[1];

    for (int j = 0; (j < 3) && (is[0] == null); j++) {
      Thread t = new Thread() {
        public void run() {
          try {
            is[0] = urlconnection.getInputStream();
          }
          catch (IOException localIOException)
          {
          }
        }
      };
      t.setName("JarInputStreamThread");
      t.start();

      int iterationCount = 0;
      while ((is[0] == null) && (iterationCount++ < 5)) {
        try {
          t.join(1000L);
        }
        catch (InterruptedException localInterruptedException)
        {
        }
      }
      if (is[0] != null) continue;
      try {
        t.interrupt();
        t.join();
      }
      catch (InterruptedException localInterruptedException1)
      {
      }
    }

    if (is[0] == null) {
      if (currentFile.equals("minecraft.jar")) {
        throw new Exception("Unable to download " + currentFile);
      }
      throw new Exception("Unable to download " + currentFile);
    }

    return is[0];
  }

// from AnjoCaido launcher  
  protected void extractLZMA(String in, String out) throws Exception
  {
    File f = new File(in);
    File fout = new File(out);
    LzmaAlone.decompress(f, fout);
    f.delete();
  }

  protected void extractPack(String in, String out)
    throws Exception
  {
    File f = new File(in);
    if (!f.exists()) return;

    FileOutputStream fostream = new FileOutputStream(out);
    JarOutputStream jostream = new JarOutputStream(fostream);

    Pack200.Unpacker unpacker = Pack200.newUnpacker();
    unpacker.unpack(f, jostream);
    jostream.close();

    f.delete();
  }

  protected void extractJars(String path)
    throws Exception
  {
    state = 5;
    
    UnZip();
    
    float increment = 10.0F / urlList.length;

    for (int i = 0; i < urlList.length; i++) {
      percentage = (55 + (int)(increment * (i + 1)));
      String filename = getFileName(urlList[i]);

      if (filename.endsWith(".pack.lzma")) {
        subtaskMessage = ("Extracting: " + filename + " to " + filename.replaceAll(".lzma", ""));
        extractLZMA(path + filename, path + filename.replaceAll(".lzma", ""));

        subtaskMessage = ("Extracting: " + filename.replaceAll(".lzma", "") + " to " + filename.replaceAll(".pack.lzma", ""));
        extractPack(path + filename.replaceAll(".lzma", ""), path + filename.replaceAll(".pack.lzma", ""));
      } else if (filename.endsWith(".pack")) {
        subtaskMessage = ("Extracting: " + filename + " to " + filename.replace(".pack", ""));
        extractPack(path + filename, path + filename.replace(".pack", ""));
      } else if (filename.endsWith(".lzma")) {
        subtaskMessage = ("Extracting: " + filename + " to " + filename.replace(".lzma", ""));
        extractLZMA(path + filename, path + filename.replace(".lzma", ""));
      }
    }
  }


protected void extractNatives(String path) throws Exception
  {
    state = 5;

    int initialPercentage = percentage;

    String nativeJar = getJarName(urlList[(urlList.length - 1)]);

    Certificate[] certificate = Launcher.class.getProtectionDomain().getCodeSource().getCertificates();

    if (certificate == null) {
      URL location = Launcher.class.getProtectionDomain().getCodeSource().getLocation();

      JarURLConnection jurl = (JarURLConnection)new URL("jar:" + location.toString() + "!/net/minecraft/Launcher.class").openConnection();
      jurl.setDefaultUseCaches(true);
      try {
        certificate = jurl.getCertificates();
      }
      catch (Exception localException)
      {
      }
    }
    File nativeFolder = new File(path + "natives");
    if (!nativeFolder.exists()) {
      nativeFolder.mkdir();
    }

    File file = new File(path + nativeJar);
    if (!file.exists()) return;
    JarFile jarFile = new JarFile(file, true);
    Enumeration<?> entities = jarFile.entries();

    totalSizeExtract = 0;

    while (entities.hasMoreElements()) {
      JarEntry entry = (JarEntry)entities.nextElement();

      if ((entry.isDirectory()) || (entry.getName().indexOf('/') != -1)) {
        continue;
      }
      totalSizeExtract = (int)(totalSizeExtract + entry.getSize());
    }

    currentSizeExtract = 0;

    entities = jarFile.entries();

    while (entities.hasMoreElements()) {
      JarEntry entry = (JarEntry)entities.nextElement();

      if ((entry.isDirectory()) || (entry.getName().indexOf('/') != -1))
      {
        continue;
      }
      File f = new File(path + "natives" + File.separator + entry.getName());
      if ((f.exists()) && 
        (!f.delete()))
      {
        continue;
      }

      InputStream in = jarFile.getInputStream(jarFile.getEntry(entry.getName()));
      OutputStream out = new FileOutputStream(path + "natives" + File.separator + entry.getName());

      byte[] buffer = new byte[65536];
      int bufferSize;
      while ((bufferSize = in.read(buffer, 0, buffer.length)) != -1)
      {
        //int bufferSize;
        out.write(buffer, 0, bufferSize);
        currentSizeExtract += bufferSize;

        percentage = (initialPercentage + currentSizeExtract * 20 / totalSizeExtract);
        subtaskMessage = ("Extracting: " + entry.getName() + " " + currentSizeExtract * 100 / totalSizeExtract + "%");
      }

      validateCertificateChain(certificate, entry.getCertificates());

      in.close();
      out.close();
    }
    subtaskMessage = "";

    jarFile.close();

    File f = new File(path + nativeJar);
    f.delete();
  }

  protected static void validateCertificateChain(Certificate[] ownCerts, Certificate[] native_certs)
    throws Exception
  {
    if (ownCerts == null) return;
    if (native_certs == null) throw new Exception("Unable to validate certificate chain. Native entry did not have a certificate chain at all");

    if (ownCerts.length != native_certs.length) throw new Exception("Unable to validate certificate chain. Chain differs in length [" + ownCerts.length + " vs " + native_certs.length + "]");

    for (int i = 0; i < ownCerts.length; i++)
      if (!ownCerts[i].equals(native_certs[i]))
        throw new Exception("Certificate mismatch: " + ownCerts[i] + " != " + native_certs[i]);
  }

  protected String getJarName(URL url)
  {
    String fileName = url.getFile();

    if (fileName.contains("?")) {
      fileName = fileName.substring(0, fileName.indexOf("?"));
    }
    if (fileName.endsWith(".pack.lzma"))
      fileName = fileName.replaceAll(".pack.lzma", "");
    else if (fileName.endsWith(".pack"))
      fileName = fileName.replaceAll(".pack", "");
    else if (fileName.endsWith(".lzma")) {
      fileName = fileName.replaceAll(".lzma", "");
    }

    return fileName.substring(fileName.lastIndexOf('/') + 1);
  }

  protected String getFileName(URL url) {
    String fileName = url.getFile();
    if (fileName.contains("?")) {
      fileName = fileName.substring(0, fileName.indexOf("?"));
    }
    return fileName.substring(fileName.lastIndexOf('/') + 1);
  }

  protected void fatalErrorOccured(String error, Exception e) {
    e.printStackTrace();
    fatalError = true;
    fatalErrorDescription = ("Fatal error occured (" + state + "): " + error);
    System.out.println(fatalErrorDescription);

    System.out.println(generateStacktrace(e));
  }

  public boolean canPlayOffline()
  {
    try
    {
      String path = (String)AccessController.doPrivileged(new PrivilegedExceptionAction<Object>() {
        public Object run() throws Exception {
          return Util.getWorkingDirectory() + File.separator + "bin" + File.separator;
        }
      });
      File dir = new File(path);
      if (!dir.exists()) return false;

      dir = new File(dir, "version-mcss");
      if (!dir.exists()) return false;

      if (dir.exists()) {
        String version = readVersionFile(dir);
        if ((version != null) && (version.length() > 0))
          return true;
      }
    }
    catch (Exception e) {
      e.printStackTrace();
      return false;
    }
    return false;
  }
/**
 * Разархивирует файл client.zip из папки bin в .minecraft
 * @author ddark008
 * @throws PrivilegedActionException
 */
protected void UnZip() throws PrivilegedActionException
  {
    String szZipFilePath;
    String szExtractPath;
    String path = (String)AccessController.doPrivileged(new PrivilegedExceptionAction<Object>() {
        public Object run() throws Exception {
          return Util.getWorkingDirectory() + File.separator;
        }
      }); 
    int i;
    
    szZipFilePath = path + "bin" + File.separator + "client.zip";
      
    File f = new File(szZipFilePath);
    if(!f.exists())
    {
      System.out.println(
	"\nNot found: " + szZipFilePath);
    }
      
    if(f.isDirectory())
    {
      System.out.println(
	"\nNot file: " + szZipFilePath);
    }
    
    System.out.println(
      "Enter path to extract files: ");
    szExtractPath = path;
    
    File f1 = new File(szExtractPath);
    if(!f1.exists())
    {
      System.out.println(
	"\nNot found: " + szExtractPath);
    }
      
    if(!f1.isDirectory())
    {
      System.out.println(
	"\nNot directory: " + szExtractPath);
    }
    
    ZipFile zf; 
    Vector zipEntries = new Vector();
       
    try
    {  
      zf = new ZipFile(szZipFilePath);    
      Enumeration en = zf.entries();
      
      while(en.hasMoreElements())
      {
        zipEntries.addElement(
	  (ZipEntry)en.nextElement());
      }
      
      for (i = 0; i < zipEntries.size(); i++)
      {
        ZipEntry ze = 
	  (ZipEntry)zipEntries.elementAt(i);
	  
        extractFromZip(szZipFilePath, szExtractPath,
	  ze.getName(), zf, ze);
      }
      
      zf.close();
      System.out.println("Done!");
    }
    catch(Exception ex)
    {
      System.out.println(ex.toString());
    }
    f.delete();
  }
  
  // ============================================
  // extractFromZip
  // ============================================
  static void extractFromZip(
    String szZipFilePath, String szExtractPath,
    String szName,
    ZipFile zf, ZipEntry ze)
  {
    if(ze.isDirectory())
      return;
      
    String szDstName = slash2sep(szName);
    
    String szEntryDir;
    
    if(szDstName.lastIndexOf(File.separator) != -1)
    {
      szEntryDir =
        szDstName.substring(
	  0, szDstName.lastIndexOf(File.separator));
    }
    else	  
      szEntryDir = "";
    
    System.out.print(szDstName);
    long nSize = ze.getSize();
    long nCompressedSize = 
      ze.getCompressedSize();
    
    System.out.println(" " + nSize + " (" +
      nCompressedSize + ")");  
  
    try
    {
       File newDir = new File(szExtractPath +
	 File.separator + szEntryDir);
	 
       newDir.mkdirs();	 
       
       FileOutputStream fos = 
	 new FileOutputStream(szExtractPath +
	 File.separator + szDstName);

       InputStream is = zf.getInputStream(ze);
       byte[] buf = new byte[1024];

       int nLength;
       
       while(true)
       {
         try
         {
	   nLength = is.read(buf);
         }	 
         catch (EOFException ex)
         {
	   break;
	 }  
	 
         if(nLength < 0) 
	   break;
         fos.write(buf, 0, nLength);
       }
       
       is.close();
       fos.close();
    }   
    catch(Exception ex)
    {
      System.out.println(ex.toString());
      //System.exit(0);
    }
  }  
  // ============================================
  // slash2sep
  // ============================================
  static String slash2sep(String src)
  {
    int i;
    char[] chDst = new char[src.length()];
    String dst;
    
    for(i = 0; i < src.length(); i++)
    {
      if(src.charAt(i) == '/')
        chDst[i] = File.separatorChar;
      else
        chDst[i] = src.charAt(i);
    }
    dst = new String(chDst);
    return dst;
  }
  

}