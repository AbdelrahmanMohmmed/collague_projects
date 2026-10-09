class NodeTree(object):

    def __init__(self, left=None, right=None):
        self.left = left
        self.right = right

    def children(self):
        return (self.left, self.right)

    def nodes(self):
        return (self.left, self.right)

    def __str__(self):
        return '%s_%s' % (self.left, self.right)


class Huffman:

    def __init__(self, text=""):
        self.text = text
        self.freq = []
        self.nodes = []
        self.huffmanCode = {}

    # Build frequency (same as your code)
    def build_frequency(self):
        freq = {}
        for c in self.text:
            if c in freq:
                freq[c] += 1
            else:
                freq[c] = 1

        # Same sorting: highest first
        self.freq = sorted(freq.items(), key=lambda x: x[1], reverse=True)
        return self.freq

    # Build Huffman Tree (same loop as your code)
    def build_tree(self):
        self.nodes = self.freq.copy()

        while len(self.nodes) > 1:
            (key1, c1) = self.nodes[-1]   # smallest
            (key2, c2) = self.nodes[-2]   # second smallest

            self.nodes = self.nodes[:-2]

            node = NodeTree(key1, key2)

            self.nodes.append((node, c1 + c2))

            self.nodes = sorted(self.nodes, key=lambda x: x[1], reverse=True)

        return self.nodes[0][0]

    # Your recursive function as a class method (unchanged)
    def huffman_code_tree(self, node, left=True, binString=''):
        if type(node) is str:
            return {node: binString}

        (l, r) = node.children()

        d = dict()
        d.update(self.huffman_code_tree(l, True, binString + '0'))
        d.update(self.huffman_code_tree(r, False, binString + '1'))
        return d

    # Generate final dictionary of codes
    def generate_codes(self):
        root = self.nodes[0][0]
        self.huffmanCode = self.huffman_code_tree(root)
        return self.huffmanCode

    # OOP-friendly print function (same format)
    def print_codes(self):
        print(' Char | Huffman code ')
        print('----------------------')
        for (char, frequency) in self.freq:
            print(' %-4r |%12s' % (char, self.huffmanCode[char]))

    # Optional: encode and decode (not modifying logic)
    def encode(self):
        return "".join(self.huffmanCode[c] for c in self.text)

    def decode(self, encoded):
        reverse = {v: k for k, v in self.huffmanCode.items()}
        buffer = ""
        output = ""
        for bit in encoded:
            buffer += bit
            if buffer in reverse:
                output += reverse[buffer]
                buffer = ""
        return output



