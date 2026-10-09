from Ai_huffman import *

text = "ABRACADABRA"
freq = build_frequency(text)
heap = build_heap(freq)
# print(freq)
# Normal (left->'0', right->'1')
codes = build_codes(heap, swap_bits=False)
print("Codes (normal):", codes)
# encoded = encode(text, codes)
# print("Encoded (normal):", encoded)
# decoded = decode(encoded, codes)
# print("Decoded (normal):", decoded)


from My_huffman import *

encode2 , tree = my_huffman_encode(text)
# print("Encoded:", encode2)
print("tree: " , tree)

from huffman_programiz import *
h = Huffman(text)
h.build_frequency()
h.build_tree()
h.generate_codes()

h.print_codes()

encoded = h.encode()
print("\nEncoded:", encoded)
print("Decoded:", h.decode(encoded))

