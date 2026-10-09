import numpy as np
def make_nonuniform_table(bit_size, data, full_scale=256, epsilon=1.0):

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
if __name__ == '__main__':
    bit_size = 2

    pixels = [6, 15, 17, 60, 100, 90, 66, 59, 18, 3, 5, 16, 14, 67, 63, 2, 98, 92]

    encoded = nonuniform_quantizer_encode(pixels, bit_size, 128)
    decoded = nonuniform_quantizer_decode(encoded, bit_size,pixels ,128)

    print("Encoded:", encoded)
    print("Decoded:", decoded)
