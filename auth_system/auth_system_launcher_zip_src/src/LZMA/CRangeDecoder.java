package LZMA;

import LZMA.CRangeDecoder;
import java.io.IOException;
import java.io.InputStream;

class CRangeDecoder
{
  static final int kNumTopBits = 24;
  static final int kTopValue = 16777216;
  static final int kTopValueMask = -16777216;
  static final int kNumBitModelTotalBits = 11;
  static final int kBitModelTotal = 2048;
  static final int kNumMoveBits = 5;
  InputStream inStream;
  int Range;
  int Code;
  byte[] buffer;
  int buffer_size;
  int buffer_ind;
  static final int kNumPosBitsMax = 4;
  static final int kNumPosStatesMax = 16;
  static final int kLenNumLowBits = 3;
  static final int kLenNumLowSymbols = 8;
  static final int kLenNumMidBits = 3;
  static final int kLenNumMidSymbols = 8;
  static final int kLenNumHighBits = 8;
  static final int kLenNumHighSymbols = 256;
  static final int LenChoice = 0;
  static final int LenChoice2 = 1;
  static final int LenLow = 2;
  static final int LenMid = 130;
  static final int LenHigh = 258;
  static final int kNumLenProbs = 514;

  CRangeDecoder(InputStream paramInputStream)
    throws IOException
  {
    buffer = new byte[16384];
    inStream = paramInputStream;
    Code = 0;
    Range = -1;
    for (int i = 0; i < 5; i++)
      Code = (Code << 8 | Readbyte());
  }

  int Readbyte() throws IOException {
    if (buffer_size == buffer_ind) {
      buffer_size = inStream.read(buffer);
      buffer_ind = 0;

      if (buffer_size < 1)
        throw new LzmaException("LZMA : Data Error");
    }
    return buffer[(buffer_ind++)] & 0xFF;
  }

  int DecodeDirectBits(int paramInt) throws IOException {
    int i = 0;
    for (int j = paramInt; j > 0; j--) {
      Range >>>= 1;
      int k = Code - Range >>> 31;
      Code -= (Range & k - 1);
      i = i << 1 | 1 - k;

      if (Range >= 16777216)
        continue;
      Code = (Code << 8 | Readbyte());
      Range <<= 8;
    }

    return i;
  }

  int BitDecode(int[] paramArrayOfInt, int paramInt) throws IOException {
    int i = (Range >>> 11) * paramArrayOfInt[paramInt];
    if ((Code & 0xFFFFFFFF) < (i & 0xFFFFFFFF))
    {
      Range = i;
      paramArrayOfInt[paramInt] += (2048 - paramArrayOfInt[paramInt] >>> 5);

      if ((Range & 0xFF000000) == 0) {
        Code = (Code << 8 | Readbyte());
        Range <<= 8;
      }
      return 0;
    }
    Range -= i;
    Code -= i;
    paramArrayOfInt[paramInt] -= (paramArrayOfInt[paramInt] >>> 5);

    if ((Range & 0xFF000000) == 0) {
      Code = (Code << 8 | Readbyte());
      Range <<= 8;
    }
    return 1;
  }

  int BitTreeDecode(int[] paramArrayOfInt, int paramInt1, int paramInt2) throws IOException
  {
    int i = 1;
    for (int j = paramInt2; j > 0; j--) {
      i = i + i + BitDecode(paramArrayOfInt, paramInt1 + i);
    }
    return i - (1 << paramInt2);
  }

  int ReverseBitTreeDecode(int[] paramArrayOfInt, int paramInt1, int paramInt2) throws IOException {
    int i = 1;
    int j = 0;

    for (int k = 0; k < paramInt2; k++) {
      int m = BitDecode(paramArrayOfInt, paramInt1 + i);
      i = i + i + m;
      j |= m << k;
    }
    return j;
  }

  byte LzmaLiteralDecode(int[] paramArrayOfInt, int paramInt) throws IOException {
    int i = 1;
    do
      i = i + i | BitDecode(paramArrayOfInt, paramInt + i);
    while (i < 256);

    return (byte)i;
  }

  byte LzmaLiteralDecodeMatch(int[] paramArrayOfInt, int paramInt, byte paramByte) throws IOException {
    int i = 1;
    do {
      int j = paramByte >> 7 & 0x1;
      paramByte = (byte)(paramByte << 1);
      int k = BitDecode(paramArrayOfInt, paramInt + (1 + j << 8) + i);
      i = i << 1 | k;

      if (j != k) {
        while (i < 256) {
          i = i + i | BitDecode(paramArrayOfInt, paramInt + i);
        }
      }
    }
    while (i < 256);

    return (byte)i;
  }

  int LzmaLenDecode(int[] paramArrayOfInt, int paramInt1, int paramInt2)
    throws IOException
  {
    if (BitDecode(paramArrayOfInt, paramInt1 + 0) == 0) {
      return BitTreeDecode(paramArrayOfInt, paramInt1 + 2 + (paramInt2 << 3), 3);
    }

    if (BitDecode(paramArrayOfInt, paramInt1 + 1) == 0) {
      return 8 + BitTreeDecode(paramArrayOfInt, paramInt1 + 130 + (paramInt2 << 3), 3);
    }

    return 16 + BitTreeDecode(paramArrayOfInt, paramInt1 + 258, 8);
  }
}