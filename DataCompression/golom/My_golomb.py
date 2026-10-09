import math

def unary_code(q):
    return '1' * q + '0'

def golomb_old_encode(N,M):
    q = N // M
    r = N % M
    quo = unary_code(q)
    b = math.floor(math.log2(M))
    c = math.ceil(math.log2(M))
    rem = bin(r)[2:]
    l = len(rem)

    if b == c:
        rem = '0'*(b-l) + rem
        return quo + rem

    k = 2 ** c - M
    if r < k: # encode r in b bits
        rem = '0' * (b - l) + rem
        return quo + rem

    rem = bin(r + k)[2:]
    l = len(rem)
    rem = '0' * (c - l) + rem
    return quo + rem

def golomb_encode(N,M=4):
    q = N // M
    r = N % M
    quo = unary_code(q)
    b = math.floor(math.log2(M))
    rem = bin(r)[2:]
    l = len(rem)

    k = 2 ** (b+1) - M
    if r < k: # encode r in b bits
        rem = '0' * (b - l) + rem
        return quo + rem

    rem = bin(r + k)[2:] #encode r+k in b+1 bits
    l = len(rem)
    rem = '0' * (b+1 - l) + rem
    return quo + rem

def golomb_my_decode(code, M=4): #need fix
    b = math.floor(math.log2(M))
    k = 2 ** (b + 1) - M
    q = 0
    i = 0 # tracker
    while i < len(code) and code[i] == '1':
    # getting q value by count number of '1' stop when see 0 also we check i < len(code) to prevent
    # infinite loop like this -> 11111
        q += 1
        i += 1
    i += 1
    if i + b > len(code): # if we got something like 11110 -> invalid
        raise ValueError("Invalid code")
    n = M*q
    r_bits = code[i:i+b]
    r = int(r_bits, 2)
    return n+r

def golomb_decode(code, M=4):
    b = math.floor(math.log2(M))
    k = 2 ** (b + 1) - M
    q = 0
    i = 0 # tracker
    while i < len(code) and code[i] == '1':
    # getting q value by count number of '1' stop when see 0 also we check i < len(code) to prevent
    # infinite loop like this -> 11111
        q += 1
        i += 1
    i += 1
    if i + b > len(code): # if we got something like 11110 -> invalid
        raise ValueError("Invalid code")

    r_bits = code[i:i+b]
    r = int(r_bits, 2)

    if r < k:
        i += b
    else:
        if i + b + 1 > len(code):
            raise ValueError("Invalid code")
        r_bits = code[i:i + b + 1]
        r = int(r_bits, 2) - k
        i += (b + 1)

    return q * M + r


# advanced
def save_golomb_to_string(data_type, M, payload):
    """
    data_type: 'numbers' or 'text'
    payload: encoded string
    """
    return f"{data_type}|M={M}\n===DATA===\n{payload}"


def load_golomb_from_string(file_content):
    header, data = file_content.split("\n===DATA===\n")
    parts = header.split("|")
    data_type = parts[0]
    M = int(parts[1].split("=")[1])
    return data_type, M, data


# ---------- HIGH LEVEL ENCODE ----------
def golomb_encode_file(content, M=4):
    # Detect if file is numbers or text
    stripped = content.strip()

    is_numeric = stripped.replace(" ", "").replace("\n", "").isdigit()
    if is_numeric:
        numbers = list(map(int, stripped.split()))
        nums_to_encode = []  # always define it

        # Check if safe for normal numeric Golomb
        max_n = max(numbers)
        if max_n // M < 10_000:
            data_type = "numbers"
            nums_to_encode = numbers
        else:
            # Too large → fall back to text mode
            data_type = "text"
            nums_to_encode = [ord(c) for c in content]

    else:
        # Text file → convert to ASCII
        numbers = [ord(c) for c in content]
        data_type = "text"
        nums_to_encode = [ord(c) for c in content]  # <-- THIS WAS MISSING

    # Encode all numbers
    encoded_bits = "".join(golomb_encode(n, M) for n in nums_to_encode)

    return save_golomb_to_string(data_type, M, encoded_bits)


# ---------- HIGH LEVEL DECODE ----------
def golomb_decode_file(file_content,M=4):
    data_type, M, bitstream = load_golomb_from_string(file_content)

    decoded_numbers = []
    i = 0

    while i < len(bitstream):
        # decode ONE value
        b = math.floor(math.log2(M))
        k = 2 ** (b + 1) - M

        # read unary
        q = 0
        while i < len(bitstream) and bitstream[i] == "1":
            q += 1
            i += 1
        i += 1  # skip '0'

        # remainder
        r_bits = bitstream[i:i+b]
        r = int(r_bits, 2)

        if r < k:
            i += b
        else:
            r_bits = bitstream[i:i + b + 1]
            r = int(r_bits, 2) - k
            i += (b + 1)

        decoded_numbers.append(q * M + r)

    if data_type == "text":
        return "".join(chr(n) for n in decoded_numbers)
    else:
        return " ".join(map(str, decoded_numbers))
