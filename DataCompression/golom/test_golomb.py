from My_golomb import *

for n in range(10):
    print(f"n={n}, m=4 → codeword={golomb_old_encode(n, 4)}")
print("\n")
for n in range(10):
    print(f"n={n}, m=5 → codeword={golomb_old_encode(n, 5)}")

print("\n")
n = 4
m = 6
code = golomb_old_encode(n,m)
print(code)
n = golomb_decode(code,m)
print(n)
print(golomb_my_decode(code,m))
