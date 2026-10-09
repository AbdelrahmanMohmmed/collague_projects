from PIL import Image
from quantizer.scaler_uniform_quantizer import *
from quantizer.scaler_nonuniform_quantizer import *

def load_image_as_matrix(path):
    """
    Loads an image and returns:
    - np array (H, W, 3)
    - original PIL image
    """
    img = Image.open(path).convert("RGB")
    matrix = np.array(img)
    return matrix, img


def quantize_image(image_matrix, bit_size=2):
    """
    Apply uniform quantizer to each RGB channel independently.
    Returns encoded image (indices) and decoded (midpoint) image.
    """

    encoded_img = np.zeros_like(image_matrix)
    decoded_img = np.zeros_like(image_matrix)

    # Process each color channel separately
    for c in range(3):  # R, G, B
        channel = image_matrix[:, :, c]

        encoded_channel = uniform_quantizer_encode(channel, bit_size)
        decoded_channel = uniform_quantizer_decode(encoded_channel, bit_size)

        encoded_img[:, :, c] = encoded_channel
        decoded_img[:, :, c] = decoded_channel

    return encoded_img, decoded_img

def quantize_image_nonuniform(image_matrix, bit_size=5):
    encoded_img = np.zeros_like(image_matrix)
    decoded_img = np.zeros_like(image_matrix)

    for c in range(3):  # R, G, B
        channel = image_matrix[:, :, c]

        # Encode & decode
        encoded_channel = nonuniform_quantizer_encode(channel, bit_size)
        decoded_channel = nonuniform_quantizer_decode(encoded_channel, bit_size,channel)

        encoded_img[:, :, c] = encoded_channel
        decoded_img[:, :, c] = decoded_channel

    return encoded_img, decoded_img


def save_image(matrix, output_path):
    """
    Save a reconstructed (decoded) image matrix as PNG/JPG
    """
    img = Image.fromarray(matrix.astype(np.uint8))
    img.save(output_path)

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