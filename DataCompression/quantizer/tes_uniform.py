from quantizer_photos import *
# Load image
matrix, img = load_image_as_matrix("/quantizer/photos/input.jpeg")

# Quantize 3-bit (8 levels)
encoded, decoded = quantize_image(matrix, bit_size=3)

# Save decoded image
save_image(decoded, "quantizer/photos/decoded_output_3bit.png")

# Encoded is stored as indices (0–7)
np.save("quantizer/photos/encoded_indices.npy", encoded)
