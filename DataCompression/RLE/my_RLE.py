class RLE:
    def __init__(self):
        pass

    def encoder(self,text):
        answer = ''
        counter = 1
        length = len(text)
        for i in range(1,length):
            if text[i] != text[i-1]:
                answer += (str(counter)+text[i-1])
                counter=0
            counter+=1
        answer += (str(counter)+text[-1])
        return answer

    def decoder(self,text:str):
        length = len(text)
        answer = ''
        for i in range(0,length,2):
            num = int(text[i])
            ch = text[i+1]
            answer+= ch*num
        return answer

rle_encoder = RLE()