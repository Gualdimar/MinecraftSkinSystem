package LZMA;

import LZMA.CRangeDecoder;
import LZMA.LzmaInputStream;
import java.io.FilterInputStream;
import java.io.IOException;
import java.io.InputStream;

public class LzmaInputStream extends FilterInputStream
{
  boolean isClosed;
  CRangeDecoder RangeDecoder;
  byte[] dictionary;
  int dictionarySize;
  int dictionaryPos;
  int GlobalPos;
  int rep0;
  int rep1;
  int rep2;
  int rep3;
  int lc;
  int lp;
  int pb;
  int State;
  boolean PreviousIsMatch;
  int RemainLen;
  int[] probs;
  byte[] uncompressed_buffer;
  int uncompressed_size;
  int uncompressed_offset;
  long GlobalNowPos;
  long GlobalOutSize;
  static final int LZMA_BASE_SIZE = 1846;
  static final int LZMA_LIT_SIZE = 768;
  static final int kBlockSize = 65536;
  static final int kNumStates = 12;
  static final int kStartPosModelIndex = 4;
  static final int kEndPosModelIndex = 14;
  static final int kNumFullDistances = 128;
  static final int kNumPosSlotBits = 6;
  static final int kNumLenToPosStates = 4;
  static final int kNumAlignBits = 4;
  static final int kAlignTableSize = 16;
  static final int kMatchMinLen = 2;
  static final int IsMatch = 0;
  static final int IsRep = 192;
  static final int IsRepG0 = 204;
  static final int IsRepG1 = 216;
  static final int IsRepG2 = 228;
  static final int IsRep0Long = 240;
  static final int PosSlot = 432;
  static final int SpecPos = 688;
  static final int Align = 802;
  static final int LenCoder = 818;
  static final int RepLenCoder = 1332;
  static final int Literal = 1846;

  public LzmaInputStream(InputStream paramInputStream)
    throws IOException
  {
    super(paramInputStream);

    isClosed = false;

    readHeader();

    fill_buffer();
  }

  private void LzmaDecode(int paramInt) throws IOException
  {
    int j = (1 << pb) - 1;
    int k = (1 << lp) - 1;

    uncompressed_size = 0;

    if (RemainLen == -1)
      return;
    int m;
    while ((RemainLen > 0) && (uncompressed_size < paramInt)) {
      m = dictionaryPos - rep0;
      if (m < 0)
        m += dictionarySize;
      byte tmp103_102 = dictionary[m]; dictionary[dictionaryPos] = tmp103_102; uncompressed_buffer[(uncompressed_size++)] = tmp103_102;
      if (++dictionaryPos == dictionarySize)
        dictionaryPos = 0;
      RemainLen -= 1;
    }
    int i;
    if (dictionaryPos == 0)
      i = dictionary[(dictionarySize - 1)];
    else {
      i = dictionary[(dictionaryPos - 1)];
    }
    while (uncompressed_size < paramInt) {
      m = uncompressed_size + GlobalPos & j;
      int n;
      int i1;
      if (RangeDecoder.BitDecode(probs, 0 + (State << 4) + m) == 0) {
        n = 1846 + 768 * (((uncompressed_size + GlobalPos & k) << lc) + ((i & 0xFF) >> 8 - lc));

        if (State < 4)
          State = 0;
        else if (State < 10)
          State -= 3;
        else
          State -= 6;
        if (PreviousIsMatch) {
          i1 = dictionaryPos - rep0;
          if (i1 < 0)
            i1 += dictionarySize;
          byte b = dictionary[i1];

          i = RangeDecoder.LzmaLiteralDecodeMatch(probs, n, b);
          PreviousIsMatch = false;
        } else {
          i = RangeDecoder.LzmaLiteralDecode(probs, n);
        }

        uncompressed_buffer[(uncompressed_size++)] = (byte) i;

        dictionary[dictionaryPos] = (byte) i;
        if (++dictionaryPos == dictionarySize)
          dictionaryPos = 0;
      }
      else {
        PreviousIsMatch = true;
        if (RangeDecoder.BitDecode(probs, 192 + State) == 1) {
          if (RangeDecoder.BitDecode(probs, 204 + State) == 0) {
            if (RangeDecoder.BitDecode(probs, 240 + (State << 4) + m) == 0)
            {
              if (uncompressed_size + GlobalPos == 0) {
                throw new LzmaException("LZMA : Data Error");
              }
              State = (State < 7 ? 9 : 11);

              n = dictionaryPos - rep0;
              if (n < 0)
                n += dictionarySize;
              i = dictionary[n];
              dictionary[dictionaryPos] = (byte) i;
              if (++dictionaryPos == dictionarySize) {
                dictionaryPos = 0;
              }
              uncompressed_buffer[(uncompressed_size++)] = (byte) i;
              continue;
            }
          }
          else {
            if (RangeDecoder.BitDecode(probs, 216 + State) == 0) {
              n = rep1;
            } else {
              if (RangeDecoder.BitDecode(probs, 228 + State) == 0) {
                n = rep2;
              } else {
                n = rep3;
                rep3 = rep2;
              }
              rep2 = rep1;
            }
            rep1 = rep0;
            rep0 = n;
          }
          RemainLen = RangeDecoder.LzmaLenDecode(probs, 1332, m);
          State = (State < 7 ? 8 : 11);
        } else {
          rep3 = rep2;
          rep2 = rep1;
          rep1 = rep0;
          State = (State < 7 ? 7 : 10);
          RemainLen = RangeDecoder.LzmaLenDecode(probs, 818, m);
          n = RangeDecoder.BitTreeDecode(probs, 432 + ((RemainLen < 4 ? RemainLen : 3) << 6), 6);

          if (n >= 4) {
            i1 = (n >> 1) - 1;
            rep0 = ((0x2 | n & 0x1) << i1);
            if (n < 14) {
              rep0 += RangeDecoder.ReverseBitTreeDecode(probs, 688 + rep0 - n - 1, i1);
            }
            else {
              rep0 += (RangeDecoder.DecodeDirectBits(i1 - 4) << 4);

              rep0 += RangeDecoder.ReverseBitTreeDecode(probs, 802, 4);
            }
          } else {
            rep0 = n;
          }rep0 += 1;
        }
        if (rep0 == 0)
        {
          RemainLen = -1;
          break;
        }
        if (rep0 > uncompressed_size + GlobalPos)
        {
          throw new LzmaException("LZMA : Data Error");
        }
        RemainLen += 2;
        do
        {
          n = dictionaryPos - rep0;
          if (n < 0)
            n += dictionarySize;
          i = dictionary[n];
          dictionary[dictionaryPos] = (byte) i;
          if (++dictionaryPos == dictionarySize) {
            dictionaryPos = 0;
          }
          uncompressed_buffer[(uncompressed_size++)] = (byte) i;
          RemainLen -= 1;
        }while ((RemainLen > 0) && (uncompressed_size < paramInt));
      }
    }

    GlobalPos += uncompressed_size;
  }

  private void fill_buffer() throws IOException {
    if (GlobalNowPos < GlobalOutSize) {
      uncompressed_offset = 0;
      long l = GlobalOutSize - GlobalNowPos;
      int i;
      if (l > 65536L)
        i = 65536;
      else {
        i = (int)l;
      }
      LzmaDecode(i);

      if (uncompressed_size == 0)
        GlobalOutSize = GlobalNowPos;
      else
        GlobalNowPos += uncompressed_size;
    }
  }

  private void readHeader() throws IOException
  {
    byte[] arrayOfByte = new byte[5];

    if (5 != in.read(arrayOfByte)) {
      throw new LzmaException("LZMA header corrupted : Properties error");
    }
    GlobalOutSize = 0L;
    for (int i = 0; i < 8; i++) {
      int j = in.read();
      if (j == -1)
        throw new LzmaException("LZMA header corrupted : Size error");
      GlobalOutSize += (j << i * 8);
    }

    if (GlobalOutSize == -1L) GlobalOutSize = 9223372036854775807L;

    int i = arrayOfByte[0] & 0xFF;
    if (i >= 225) {
      throw new LzmaException("LZMA header corrupted : Properties error");
    }

    for (pb = 0; i >= 45; i -= 45) pb += 1;

    for (lp = 0; i >= 9; i -= 9) lp += 1;

    lc = i;

    int j = 1846 + (768 << lc + lp);

    probs = new int[j];

    dictionarySize = 0;
    for (int k = 0; k < 4; k++)
      dictionarySize += ((arrayOfByte[(1 + k)] & 0xFF) << k * 8);
    dictionary = new byte[dictionarySize];
    if (dictionary == null) {
      throw new LzmaException("LZMA : can't allocate");
    }

    int k = 1846 + (768 << lc + lp);

    RangeDecoder = new CRangeDecoder(in);
    dictionaryPos = 0;
    GlobalPos = 0;
    rep0 = (this.rep1 = this.rep2 = this.rep3 = 1);
    State = 0;
    PreviousIsMatch = false;
    RemainLen = 0;
    dictionary[(dictionarySize - 1)] = 0;
    for (int m = 0; m < k; m++) {
      probs[m] = 1024;
    }
    uncompressed_buffer = new byte[65536];
    uncompressed_size = 0;
    uncompressed_offset = 0;

    GlobalNowPos = 0L;
  }

  public int read(byte[] paramArrayOfByte, int paramInt1, int paramInt2) throws IOException {
    if (isClosed) {
      throw new IOException("stream closed");
    }
    if ((paramInt1 | paramInt2 | paramInt1 + paramInt2 | paramArrayOfByte.length - (paramInt1 + paramInt2)) < 0) {
      throw new IndexOutOfBoundsException();
    }
    if (paramInt2 == 0) {
      return 0;
    }
    if (uncompressed_offset == uncompressed_size)
      fill_buffer();
    if (uncompressed_offset == uncompressed_size) {
      return -1;
    }
    int i = Math.min(paramInt2, uncompressed_size - uncompressed_offset);
    System.arraycopy(uncompressed_buffer, uncompressed_offset, paramArrayOfByte, paramInt1, i);
    uncompressed_offset += i;
    return i;
  }

  public void close() throws IOException {
    isClosed = true;
    super.close();
  }
}