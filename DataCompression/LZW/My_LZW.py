import json

def lzw_encode(text):
    # Initialize dictionary with single characters
    dictionary = {chr(i): i for i in range(256)}
    next_code = 256

    current = ""
    result = []

    for c in text:
        combined = current + c
        if combined in dictionary:
            current = combined
        else:
            result.append(dictionary[current])
            dictionary[combined] = next_code
            next_code += 1
            current = c

    if current:
        result.append(dictionary[current])

    return result


def lzw_decode(code):
    # Initialize dictionary with single characters
    dictionary = {i: chr(i) for i in range(256)}
    next_code = 256

    prev = code[0]
    result = dictionary[prev]

    for cur in code[1:]:
        if cur in dictionary:
            entry = dictionary[cur]
        else:
            # Special case for codes not yet in the dictionary
            entry = dictionary[prev] + dictionary[prev][0]

        result += entry
        dictionary[next_code] = dictionary[prev] + entry[0]
        next_code += 1

        prev = cur

    return result

#extra functions


def lzw_encode_file(text):
    """Encode text and serialize to string"""
    encoded_list = lzw_encode(text)
    return json.dumps(encoded_list)

def lzw_decode_file(file_content):
    """Deserialize and decode"""
    encoded_list = json.loads(file_content)
    return lzw_decode(encoded_list)
