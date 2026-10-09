import numpy as np

# -----------------------------------------------------
# Create Uniform Quantization Table
# Each entry maps a value range (low, high) → (index, midpoint)
# -----------------------------------------------------
def make_uniform_table(bit_size, full_scale=256):
    num_steps = 2 ** bit_size                    # Number of quantization levels
    step = full_scale // num_steps               # Step size for each interval
    table = {}

    for i in range(num_steps):
        low = i * step                           # Lower bound of interval
        high = step * (i + 1) - 1                # Upper bound of interval
        midpoint = int((low + high + 1) / 2)     # Midpoint value for decoding (Q^-1)
        table[(low, high)] = (i, midpoint)

    return table


# -----------------------------------------------------
# Uniform Quantizer Encoder
# Converts each sample to its corresponding quantization index
# -----------------------------------------------------
def uniform_quantizer_encode(data, bit_size, full_scale=256):
    table = make_uniform_table(bit_size, full_scale)
    data = np.array(data)
    encoded = np.zeros_like(data)

    # Assign each sample to its correct quantization interval
    for (low, high), (index, midpoint) in table.items():
        mask = (data >= low) & (data <= high)    # Identify samples in this interval
        encoded[mask] = index                    # Assign quantization index

    return encoded


# -----------------------------------------------------
# Uniform Quantizer Decoder
# Converts each quantization index back to its midpoint value
# -----------------------------------------------------
def uniform_quantizer_decode(encoded, bit_size, full_scale=256):
    table = make_uniform_table(bit_size, full_scale)
    decoded = np.zeros_like(encoded)

    # Create reverse lookup: index → midpoint
    index_to_mid = {v[0]: v[1] for v in table.values()}

    # Replace each index by its corresponding midpoint
    for idx, midpoint in index_to_mid.items():
        decoded[encoded == idx] = midpoint

    return decoded


# -----------------------------------------------------
# Compression Ratio Calculation
# CR = (original size in bits) / (compressed size in bits)
# -----------------------------------------------------
def compute_compression_ratio(num_samples, quantized_bits_per_sample, original_bits_per_sample=8):
    original_size_bits = num_samples * original_bits_per_sample
    compressed_size_bits = num_samples * quantized_bits_per_sample
    return original_size_bits / compressed_size_bits


# -----------------------------------------------------
# Mean Squared Error (MSE)
# Measures distortion between original and reconstructed signals
# -----------------------------------------------------
def mean_squared_error(original, reconstructed):
    original = np.array(original, dtype=float)
    reconstructed = np.array(reconstructed, dtype=float)
    return np.mean((original - reconstructed) ** 2)


# -----------------------------------------------------
# Main Program Execution
# -----------------------------------------------------
if __name__ == '__main__':
    bit_size = 2          # Number of quantization bits
    orig_bits = 8         # Original bits per sample (e.g., 8-bit pixel)

    pixels = [6, 15, 17, 60, 100, 90, 66, 59, 18, 3, 5, 16, 14, 67, 63, 2, 98, 92]

    # Encoding and Decoding
    encoded = uniform_quantizer_encode(pixels, bit_size, 128)
    decoded = uniform_quantizer_decode(encoded, bit_size, 128)

    # Compute Compression Ratio
    cr = compute_compression_ratio(len(pixels), bit_size, orig_bits)

    # Compute Mean Squared Error
    mse = mean_squared_error(pixels, decoded)

    # Output results
    print("Encoded:", encoded)
    print("Decoded:", decoded)
    print(f"Compression Ratio: {cr:.2f} : 1")
    print(f"Mean Squared Error (MSE): {mse:.4f}")
