# huffman_heap.py
import heapq
from collections import Counter
from typing import Dict, Tuple, List, Optional


def build_frequency(text: str) -> Counter:
    """Return frequency Counter of characters in text."""
    return Counter(text)


def build_heap(freq: Counter) -> List:
    """
    Build a heap where each element is:
      [weight, [char, code], [char2, code2], ...]
    Initially each char's code is "" (empty).
    """
    heap = [[weight, [char, ""]] for char, weight in freq.items()]
    heapq.heapify(heap)
    return heap


def build_codes(heap: List, swap_bits: bool = False) -> Dict[str, str]:
    """
    Build Huffman codes from the heap and return a dict char->code.
    If swap_bits is True, left branch gets '1' and right gets '0' (flipped).
    """
    if not heap:
        return {}

    # If there's only one symbol, give it code "0" (or "1" if swapped)
    if len(heap) == 1:
        weight, pair = heap[0]
        char = pair[0]
        return {char: ("1" if swap_bits else "0")}

    # Work on a copy to avoid mutating the original heap caller might keep
    heap = [list(node) for node in heap]
    heapq.heapify(heap)

    while len(heap) > 1:
        smallest = heapq.heappop(heap)   # [weight, [char, code], ...]
        secsmallest = heapq.heappop(heap)

        # Assign bit to each pair in the popped nodes.
        # By convention here: smallest -> left (add "0"), secsmallest -> right (add "1")
        left_bit, right_bit = ("1", "0") if swap_bits else ("0", "1")

        for pair in smallest[1:]:
            pair[1] = left_bit + pair[1]   # prepend because we're building from leaves up

        for pair in secsmallest[1:]:
            pair[1] = right_bit + pair[1]

        merged_weight = smallest[0] + secsmallest[0]
        # merged node contains combined pairs
        merged_node = [merged_weight] + smallest[1:] + secsmallest[1:]
        heapq.heappush(heap, merged_node)

    # heap now has one element: [total_weight, [char, code], ...]
    final_pairs = heap[0][1:]
    return dict((char, code) for char, code in final_pairs)


def encode(text: str, codes: Dict[str, str]) -> str:
    """Encode text to a bitstring using the codes dict."""
    return "".join(codes[ch] for ch in text)


def decode(bitstring: str, codes: Dict[str, str]) -> str:
    """
    Decode a bitstring using codes dict.
    This builds the reverse mapping and then reads bits greedily.
    """
    if not bitstring:
        return ""

    reverse = {v: k for k, v in codes.items()}

    decoded_chars = []
    buffer = ""
    for bit in bitstring:
        buffer += bit
        if buffer in reverse:
            decoded_chars.append(reverse[buffer])
            buffer = ""
    # If buffer is non-empty here, the bitstring was invalid / incomplete.
    if buffer:
        raise ValueError("Incomplete bitstring — remaining bits do not map to any symbol.")
    return "".join(decoded_chars)


