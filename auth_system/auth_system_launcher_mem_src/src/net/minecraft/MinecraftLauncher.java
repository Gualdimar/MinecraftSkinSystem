package net.minecraft;

import java.util.ArrayList;

public class MinecraftLauncher
{
  //private static final int MIN_HEAP = 511;
  //private static final int RECOMMENDED_HEAP = 1024;
	public static int memory;

  public static void main(String[] args)
    throws Exception
  {
    float heapSizeMegs = (float)(Runtime.getRuntime().maxMemory() / 1024L / 1024L);

    memory = Util.getMemorySelection();
    if (memory == 1)
    	memory = 1024;

    if (heapSizeMegs > 511.0F)
      LauncherFrame.main(args);
    else
      try {
        String pathToJar = MinecraftLauncher.class.getProtectionDomain().getCodeSource().getLocation().toURI().getPath();

        ArrayList<String> params = new ArrayList<String>();

        if (Util.getPlatform() == Util.OS.windows) {
			params.add("javaw");
		} else {
			params.add("java");
		}
        params.add("-Xmx" + memory + "m");
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