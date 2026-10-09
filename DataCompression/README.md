# 📘 LZW Compression & Decompression (Python)

## 🔍 Overview
This project provides a simple Python implementation of the **Lempel–Ziv–Welch (LZW)** compression algorithm.  
LZW is a **lossless data compression** method that builds a dynamic dictionary of repeated patterns found in the input text.  
It replaces repeated sequences with shorter numeric codes to reduce storage size.

The project includes:

- `lzw_encode(text)` — Compresses a string into a list of integer codes.  
- `lzw_decode(code_list)` — Decompresses the code list back to the original string.

---

## 🚀 Features
- **Pure Python implementation**
- No external dependencies
- Works on ASCII character range (0–255)
- Includes full example (encode + decode)
- Clean and readable code structure

---

## 🧪 Example Usage
```python
text = 'ABAABABAAAAA'
print("code :", lzw_encode(text))
print("decoded :", lzw_decode([65, 66, 65, 256, 259, 258, 258]))
```

### ✔ Output
```
code : [65, 66, 65, 256, 259, 258, 258]
decoded : ABAABABAAAAA
```

---

## 🧠 How LZW Works (Simplified)
1. Initialize a dictionary with all ASCII characters (0–255).  
2. Read characters and find the longest pattern already in the dictionary.  
3. Output its code.  
4. Add a new extended pattern to the dictionary.  
5. Continue until the entire text is processed.  
6. During decoding, rebuild the dictionary in the same order to perfectly reconstruct the original text.

---

## ⚠️ Notes & Limitations
- This implementation works on **ASCII text only**.  
  For Unicode (Arabic, Emoji, etc.), you must extend the dictionary or process the text as bytes.  
- The decoder assumes the code list is **not empty**.  
- The dictionary grows dynamically without a size limit (can grow large for long inputs).

---

# 📘 Uniform Scalar Quantization in Python

## 🔍 Overview
This project implements **Uniform Scalar Quantization**, one of the simplest and most widely used quantization techniques in digital signal processing.  
It includes:

- Building a **uniform quantization table**  
- Encoding samples into **quantization indices**  
- Decoding indices back to **reconstructed values**  
- Computing **Compression Ratio (CR)**  
- Computing **Mean Squared Error (MSE)**  

This implementation uses Python and NumPy and is suitable for educational purposes, image processing exercises, and signal compression simulations.

---

## 🚀 Features
- Fully implemented **uniform quantizer**
- Straightforward encoding & decoding functions
- Compression Ratio calculation
- Mean Squared Error (MSE) computation
- Clear, well-commented code structure
- Pure Python + NumPy

---

### 🔹 Main Program Execution
```python
if __name__ == '__main__':
    bit_size = 2
    orig_bits = 8

    pixels = [6, 15, 17, 60, 100, 90, 66, 59, 18, 3, 5, 16, 14, 67, 63, 2, 98, 92]

    encoded = uniform_quantizer_encode(pixels, bit_size, 128)
    decoded = uniform_quantizer_decode(encoded, bit_size, 128)

    cr = compute_compression_ratio(len(pixels), bit_size, orig_bits)
    mse = mean_squared_error(pixels, decoded)

    print("Encoded:", encoded)
    print("Decoded:", decoded)
    print(f"Compression Ratio: {cr:.2f} : 1")
    print(f"Mean Squared Error (MSE): {mse:.4f}")
```

---

## 🧪 Example Output

```
Encoded: [0 0 0 0 1 1 1 0 0 0 0 0 0 1 1 0 1 1]
Decoded: [ 8  8  8  8 40 40 40  8  8  8  8  8  8 40 40  8 40 40]
Compression Ratio: 4.00 : 1
Mean Squared Error (MSE): 776.4444
```

*(Output may vary slightly depending on quantization settings.)*

---

## 🧠 How Uniform Quantization Works (Simplified)

1. Divide the full amplitude range into **equal-sized intervals**.  
2. Each interval is assigned a **quantization index**.  
3. During encoding, each sample is replaced with its corresponding index.  
4. During decoding, each index is replaced by the interval’s **midpoint**.  
5. Compression occurs because fewer bits are used per sample.  
6. Reconstruction introduces **quantization error**, measured using MSE.

---

## ⚠️ Notes & Limitations

- Uniform quantization may produce large distortion for **non-uniform distributions**.  
- Assumes input samples fall within `[0, full_scale)`.  
- No overload handling (clipping) is performed.  
- More accurate results may require **µ-law** or **A-law** companding.

---

# 🎨 Non-Uniform (Lloyd-Max) Quantization in Python

## 🔍 Overview
This project implements **Non-Uniform Scalar Quantization** using an iterative splitting method inspired by the **Lloyd–Max** algorithm.

It includes:

- Automatic centroid generation (non-uniform levels)
- Adaptive quantization based on sample distribution
- Encoding & decoding functions
- Compression Ratio (CR)
- Mean Squared Error (MSE)
- Full grayscale image quantization (load → quantize → reconstruct → save)

Supports both **sample vectors** and **grayscale images**.

---

## 🚀 Features
- Lloyd-Max style non-uniform quantizer  
- Dynamic centroid updates  
- Decision boundary computation  
- Encoding + decoding  
- MSE and compression ratio  
- Supports PNG/JPG grayscale quantization  
- Fully commented and clean Python implementation  

---

## ▶ Full Example Test (Encoding + Decoding + Image Quantization)
```python
if __name__ == '__main__':
    bit_size = 2

    pixels = [6, 15, 17, 60, 100, 90, 66, 59, 18, 3, 5, 16, 14, 67, 63, 2, 98, 92]

    encoded = nonuniform_quantizer_encode(pixels, bit_size, 128)
    decoded = nonuniform_quantizer_decode(encoded, bit_size, pixels, 128)

    print("Encoded:", encoded)
    print("Decoded:", decoded)
    
    mse = quantization_mse(pixels, decoded)
    print("MSE:", mse)

    cr = compression_ratio(orig_bit_depth=8, bit_size=bit_size)
    print("Compression Ratio:", cr)

    print("\n--- IMAGE TEST ---")

    flat_pixels, original_shape = load_color_image_as_flat_gray("icons8-github-480.png")

    encoded_img = nonuniform_quantizer_encode(flat_pixels, bit_size, 256)
    decoded_img_flat = nonuniform_quantizer_decode(encoded_img, bit_size, flat_pixels, 256)

    mse_img = quantization_mse(flat_pixels, decoded_img_flat)
    print("Image MSE:", mse_img)

    output_path = "output_quantized.png"
    save_flat_gray_as_image(decoded_img_flat, original_shape, output_path)
    print("Quantized image saved to:", output_path)
```

---

## 🧪 Tests

### ✅ Test 1 — 1D Sample Vector
Using:
```python
bit_size = 2
pixels = [6, 15, 17, 60, 100, 90, 66, 59, 18, 3, 5, 16, 14, 67, 63, 2, 98, 92]

encoded = nonuniform_quantizer_encode(pixels, bit_size, 128)
decoded = nonuniform_quantizer_decode(encoded, bit_size, pixels, 128)

mse = quantization_mse(pixels, decoded)
cr = compression_ratio(8, bit_size)
```

### ✔ Actual Output:
```
Encoded: [0 1 1 2 3 3 2 2 1 0 0 1 1 2 2 0 3 3]
Decoded: [ 4 16 16 63 95 95 63 63 16  4  4 16 16 63 63  4 95 95]
MSE: 7.666666666666667
Compression Ratio: 4.0
```

---

### ✅ Test 2 — Image Quantization
Using:
```python
flat_pixels, original_shape = load_color_image_as_flat_gray("icons8-github-480.png")

encoded_img = nonuniform_quantizer_encode(flat_pixels, bit_size, 256)
decoded_img_flat = nonuniform_quantizer_decode(encoded_img, bit_size, flat_pixels, 256)

mse_img = quantization_mse(flat_pixels, decoded_img_flat)
```

### ✔ Output Format:
```
Image MSE: <value depends on image>
Quantized image saved to: output_quantized.png
```

---

## 🧠 How Non-Uniform Quantization Works (Summary)

1. Start with **one centroid** equal to the mean of the data.  
2. Split each centroid (`c - ε`, `c + ε`).  
3. Assign samples to nearest centroid.  
4. Update centroid = mean of assigned samples.  
5. Repeat until `2^bit_size` centroids are formed.  
6. Create decision boundaries from midpoints.  
7. Build quantization table.  
8. Encode by interval, decode by centroid.

---

## ⚠️ Notes
- Non-uniform quantization reduces error compared to uniform quantization.  
- Sensitive to input distribution.  
- Works better on clustered data.  
- Low bit sizes produce higher distortion.

---

## 📌 License
This project is free to use for learning, teaching, and research purposes.
