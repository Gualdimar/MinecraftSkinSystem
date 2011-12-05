package net.minecraft;

import java.awt.Color;
import javax.swing.JLabel;

public class TransparentLabel extends JLabel
{
  private static final long serialVersionUID = 1L;

  public TransparentLabel(String string, int center)
  {
    super(string, center);
    setForeground(Color.WHITE);
  }

  public TransparentLabel(String string) {
    super(string);
    setForeground(Color.WHITE);
  }

  public boolean isOpaque() {
    return false;
  }
}