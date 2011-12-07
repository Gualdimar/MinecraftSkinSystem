package net.minecraft;

import java.util.ArrayList;

public class MinecraftLauncher
{
  //private static final int MIN_HEAP = 511;
  //private static final int RECOMMENDED_HEAP = 1024;

  public static void main(String[] args)
    throws Exception
  {
    float heapSizeMegs = (float)(Runtime.getRuntime().maxMemory() / 1024L / 1024L);

    if (heapSizeMegs > 511.0F)
      LauncherFrame.main(args);
    else
      try {
        String pathToJar = MinecraftLauncher.class.getProtectionDomain().getCodeSource().getLocation().toURI().getPath();

        ArrayList<String> params = new ArrayList<String>();

        params.add("javaw");
        params.add("-Xms512m");
        params.add("-Xmx1024m");
        params.add("-Dsun.java2d.noddraw=true");
        params.add("-Dsun.java2d.d3d=false");
        params.add("-Dsun.java2d.opengl=false");
        params.add("-Dsun.java2d.pmoffscreen=false");
        params.add("-classpath");
        params.add(pathToJar);
        params.add("net.minecraft.LauncherFrame");

        ProcessBuilder pb = new ProcessBuilder(params);
        Process process = pb.start();
        if (process == null) throw new Exception("!");
        System.exit(0);
      } catch (Exception e) {
        e.printStackTrace();
        LauncherFrame.main(args);
      }
  }
}