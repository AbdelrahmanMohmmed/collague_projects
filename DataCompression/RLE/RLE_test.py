from my_RLE import *

text = 'AAAABBBCCDAA'

myRle = RLE()
encode = myRle.encoder(text)
print("encoding : ",encode)
decode = myRle.decoder(encode)
print("decoding : ",decode)