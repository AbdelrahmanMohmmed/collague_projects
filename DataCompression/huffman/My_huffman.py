def frequency_character(text:str):
    prob = {}
    for c in text:
        if c in prob:
            prob[c] +=1
        else:
            prob[c] = 1
    return prob

def converter(freq:dict):
    heap = []
    for k,v in freq.items():
        heap.append([v,[k,'']])
    heap = sorted(heap,reverse=True)
    return heap

def encoding(text:str,Adjheap:dict):
    answer = ''
    for c in text:
        answer+=Adjheap[c]
    return answer
def huffman(heap, length):
    count = len(heap)
    ch = ''
    for item in heap:
        value = item[0]
        reminder = length - value
        if count ==1 :
            item[1][1] = ch
            break
        if value <= reminder :
            item[1][1] = ch+'0'
            ch+='1'
        else:
            item[1][1] = ch +'1'
            ch +='0'
        count-=1
        length -= value
    AdjHeap = {}
    for item in heap:
        AdjHeap[item[1][0]] = item[1][1]
    return AdjHeap

def my_huffman_encode(text):
    length = len(text)
    prob = frequency_character(text)
    heap = converter(prob)
    AdjHeap = huffman(heap,length)
    encode = encoding(text,AdjHeap)
    return encode , AdjHeap

def my_huffman_decode(encode:str,tree:dict):
    reverse_tree = {v: k for k, v in tree.items()}

    decoded_text = ""
    current_code = ""

    for bit in encode:
        current_code += bit

        # If current_code matches a Huffman code, decode it
        if current_code in reverse_tree:
            decoded_text += reverse_tree[current_code]
            current_code = ""

    return decoded_text

#helper functions
def save_huffman_to_string(code_map, encoded_data):
    return f"{code_map}\n===DATA===\n{encoded_data}"

def load_huffman_from_string(file_content):
    header, data = file_content.split("\n===DATA===\n")
    code_map = eval(header)  # safe if only your code writes files
    return code_map, data

def huffman_encode_with_tree(text):
    encoded_data, code_map = my_huffman_encode(text)
    return save_huffman_to_string(code_map, encoded_data)

def huffman_decode_with_tree(file_content):
    code_map, encoded_data = load_huffman_from_string(file_content)
    return my_huffman_decode(encoded_data, code_map)

if __name__ == '__main__':
    text1 =  'BCAADDDCCACACAC'
    text2 = "hello world"
    encode , tree = my_huffman_encode(text2)
    print(encode)
    print(my_huffman_decode(encode,tree))



