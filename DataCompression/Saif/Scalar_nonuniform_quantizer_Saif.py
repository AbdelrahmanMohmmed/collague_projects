import numpy as np
from PIL import Image

def make_nonuniform_table(bit_size, data, full_scale=128, epsilon=1.0):

    x = np.array(data, dtype=float).flatten()
    x = np.clip(x, 0, full_scale - 1)

    L = 2 ** bit_size          # number of levels

    # 1) Start with one centroid = global average
    centroids = np.array([x.mean()], dtype=float)

    # 2) Splitting until we reach L centroids
    while len(centroids) < L:
        new_centroids = []
        for c in centroids:
            new_centroids.append(c - epsilon)
            new_centroids.append(c + epsilon)

        centroids = np.array(new_centroids, dtype=float)

        # Associate
        distances = np.abs(x[:, None] - centroids[None, :])
        labels = np.argmin(distances, axis=1)

        # Average
        for k in range(len(centroids)):
            cluster_points = x[labels == k]
            if len(cluster_points) > 0:
                centroids[k] = cluster_points.mean()

    # 3) Sort centroids
    centroids = np.sort(centroids)
    L = len(centroids)

    # 4) Decision boundaries = midpoints between centroids
    boundaries = np.zeros(L + 1, dtype=float)
    boundaries[0] = 0.0
    boundaries[-1] = float(full_scale)

    for i in range(1, L):
        boundaries[i] = 0.5 * (centroids[i - 1] + centroids[i])

    # 5) Build table
    table = {}
    for i in range(L):
        low = int(np.floor(boundaries[i]))
        high = int(np.floor(boundaries[i + 1])) if i < L - 1 else int(full_scale)
        q_inv = int(np.round(centroids[i]))
        table[(low, high)] = (i, q_inv)

    return table


def nonuniform_quantizer_encode(data, bit_size, full_scale=256):
    data = np.array(data)
    encoded = np.zeros_like(data)
    table = make_nonuniform_table(bit_size,data,full_scale)

    for (low, high), (index, centroid) in table.items():
        mask = (data >= low) & (data < high)
        encoded[mask] = index

    return encoded

def nonuniform_quantizer_decode(encoded, bit_size, data, full_scale=256):
    decoded = np.zeros_like(encoded)
    table = make_nonuniform_table(bit_size,data,full_scale)

    index_to_centroid = {v[0]: v[1] for v in table.values()}

    for idx, centroid in index_to_centroid.items():
        decoded[encoded == idx] = centroid

    return decoded


# ------------------------
# Mean Squared Error (MSE)
# ------------------------
def quantization_mse(original, reconstructed):
    """
    Compute Mean Squared Error between original and reconstructed signals.
    """
    original = np.array(original, dtype=float)
    reconstructed = np.array(reconstructed, dtype=float)

    if original.shape != reconstructed.shape:
        raise ValueError("original and reconstructed must have the same shape")

    mse = np.mean((original - reconstructed) ** 2)
    return mse


# -----------------
# Compression Ratio
# -----------------
def compression_ratio(orig_bit_depth, bit_size):
    return orig_bit_depth / bit_size

# Convert image to flat gray scale and back
def load_color_image_as_flat_gray(image_path):
    """
    1) Read color image from path
    2) Convert to grayscale
    3) Convert to numpy array
    4) Save original 2D shape
    5) Flatten to 1D vector
    """
    # Read image and convert to grayscale ("L" mode)
    img = Image.open(image_path).convert("L")   #  RGB → Gray

    # Convert to numpy array (2D)
    img_np = np.array(img)          # shape: (height, width)

    # Save original shape to reconstruct later
    original_shape = img_np.shape

    # Flatten 2D → 1D
    flat_pixels = img_np.flatten()  # shape: (height*width,)

    return flat_pixels, original_shape

# Save flat gray scale pixels as image
def save_flat_gray_as_image(flat_pixels, original_shape, output_path):
    """
    1) Reshape flat 1D pixels back to 2D image
    2) Convert to uint8
    3) Save as image file
    """
    img_np = np.array(flat_pixels, dtype=np.float32).reshape(original_shape)
    img_np = np.clip(img_np, 0, 255).astype(np.uint8)

    img = Image.fromarray(img_np, mode="L")
    img.save(output_path)


if __name__ == '__main__':
    bit_size = 2

    pixels = [6, 15, 17, 60, 100, 90, 66, 59, 18, 3, 5, 16, 14, 67, 63, 2, 98, 92]

    encoded = nonuniform_quantizer_encode(pixels, bit_size, 128)
    decoded = nonuniform_quantizer_decode(encoded, bit_size,pixels ,128)

    print("Encoded:", encoded)
    print("Decoded:", decoded)
    
    mse = quantization_mse(pixels, decoded)
    print("MSE:", mse)

    cr = compression_ratio(orig_bit_depth=8, bit_size=bit_size)
    print("Compression Ratio:", cr)


    print("\n--- IMAGE TEST ---")
    # 1) Load the color image → convert to gray → flatten
    flat_pixels, original_shape = load_color_image_as_flat_gray("OIP.png")

    # 2) Encode using non-uniform quantizer
    encoded_img = nonuniform_quantizer_encode(flat_pixels, bit_size, 256)

    # 3) Decode
    decoded_img_flat = nonuniform_quantizer_decode(encoded_img, bit_size, flat_pixels, 256)

    # 4) Compute MSE
    mse_img = quantization_mse(flat_pixels, decoded_img_flat)
    print("Image MSE:", mse_img)

    # 5) Reconstruct and save the output image
    output_path = "output_quantized2.png"
    save_flat_gray_as_image(decoded_img_flat, original_shape, output_path)
    print("Quantized image saved to:", output_path)

