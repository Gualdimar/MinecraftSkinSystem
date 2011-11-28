package net.minecraft;

import java.awt.Color;
import javax.swing.JCheckBox;

public class TransparentCheckbox extends JCheckBox
{
  private static final long serialVersionUID = 1L;

  public TransparentCheckbox(String string)
  {
    super(string);
    setForeground(Color.WHITE);
  }

  public boolean isOpaque() {
    return false;
  }
}