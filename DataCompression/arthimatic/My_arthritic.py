probs = {'A':0.2,'B':0.5,'C':0.3}
code = 0.285
len = 3

def make_interval(probs):#convert probablity into cumulative ex: [0.5,0.2,0.3] -> [0.5,0.8,1]
  interval  = {}
  low = 0
  for c,p in probs.items():
    low,high = low,p+low
    interval[c] = (low,high)
    low = high
    if high > 1 :
      raise Exception('make sure cumulative probablites not greater than 1')
    if  low < 0 :
      raise Exception('there is no probabilty with negative')
  return interval

print(make_interval(probs))

def decode_arithmatic(probs,code,len):
  interval = make_interval(probs)
  massege = []
  new_interval = interval.copy()
  for step in range(len):
    for c,p in new_interval.items():
      if p[1]>code:
        massege.append(c)
        new_low,new_high = p
        if step+1 == len:
            return ''.join(massege)
        for c,p in interval.items():
          new_interval[c] = equation2(new_low,new_high,p)
        break
    print(new_interval)

def equation1(low,high,target):
  return low+(high-low)*target
def equation2(low,high,target):
  return (equation1(low,high,target[0]),equation1(low,high,target[1]))

print(decode_arithmatic(probs,code,len))

probs = {'A':0.5,'B':0.3,'C':0.2}
code = 0.385
len = 3

print(make_interval(probs))

print(decode_arithmatic(probs,code,len))