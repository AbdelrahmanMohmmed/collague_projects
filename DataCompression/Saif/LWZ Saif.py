def lzw_encode(text):
    # Initialize dictionary with all single ASCII characters
    dictionary = {chr(i): i for i in range(256)}
    next_code = 256  # Next available code

    current = ""
    result = []

    for c in text:
        combined = current + c
        if combined in dictionary:
            # If the combined string already exists, continue building it
            current = combined
        else:
            # Output code of the current string
            result.append(dictionary[current])

            # Add new string to the dictionary
            dictionary[combined] = next_code
            next_code += 1

            # Start new sequence
            current = c

    # Output last code
    if current:
        result.append(dictionary[current])

    return result


def lzw_decode(code):
    # Initialize dictionary with single ASCII characters
    dictionary = {i: chr(i) for i in range(256)}
    next_code = 256  # Next available code

    prev = code[0]      # First code
    result = dictionary[prev]  # First character

    for cur in code[1:]:
        if cur in dictionary:
            # Normal case: code exists in dictionary
            entry = dictionary[cur]
        else:
            # Special case: code not yet added (KwKwK case)
            entry = dictionary[prev] + dictionary[prev][0]

        # Add decoded entry to result
        result += entry

        # Add new sequence to dictionary
        dictionary[next_code] = dictionary[prev] + entry[0]
        next_code += 1

        prev = cur  # Move to next code

    return result


# Example
text = 'ABAABABAAAAA'
print("code :", lzw_encode(text))
print("decoded :", lzw_decode([65, 66, 65, 256, 259, 258, 258]))
