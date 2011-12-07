package net.minecraft;

import javax.swing.JButton;

public class TransparentButton extends JButton
{
  private static final long serialVersionUID = 1L;

  public TransparentButton(String string)
  {
    super(string);
  }

  public boolean isOpaque() {
    return false;
  }
}