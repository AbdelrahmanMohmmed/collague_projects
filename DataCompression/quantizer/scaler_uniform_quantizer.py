import numpy as np

def make_uniform_table(bit_size, full_scale=256):
    num_steps = 2 ** bit_size
    step = full_scale // num_steps
    table = {}

    for i in range(num_steps):
        low = i * step
        high = step * (i + 1) - 1
        midpoint = int((low + high+1) / 2)
        table[(low, high)] = (i, midpoint)

    return table

def uniform_quantizer_encode(data, bit_size, full_scale=256):
    table = make_uniform_table(bit_size,full_scale)
    data = np.array(data)
    encoded = np.zeros_like(data)

    for (low, high), (index, midpoint) in table.items():
        mask = (data >= low) & (data <= high)
        encoded[mask] = index

    return encoded

def uniform_quantizer_decode(encoded, bit_size, full_scale=256):
    table = make_uniform_table(bit_size, full_scale)
    decoded = np.zeros_like(encoded)

    # Reverse lookup: index -> midpoint
    index_to_mid = {v[0]: v[1] for v in table.values()}

    for idx, midpoint in index_to_mid.items():
        decoded[encoded == idx] = midpoint

    return decoded



if __name__ == '__main__':
    bit_size = 2

    pixels = [6, 15, 17, 60, 100, 90, 66, 59, 18, 3, 5, 16, 14, 67, 63, 2, 98, 92]

    encoded = uniform_quantizer_encode(pixels, bit_size, 128)
    decoded = uniform_quantizer_decode(encoded, bit_size, 128)

    print("Encoded:", encoded)
    print("Decoded:", decoded)

