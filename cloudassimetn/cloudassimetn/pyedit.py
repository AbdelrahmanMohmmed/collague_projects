import re
from collections import Counter

# Read the contents of the "random_paragraphs.txt" file
def read_file(file_path):
    try:
        with open(file_path, 'r', encoding='utf-8') as file:
            return file.read()
    except FileNotFoundError:
        print(f"File '{file_path}' not found.")
        return ""

# Remove stop words (you can customize this list)
def remove_stop_words(text, stop_words):
    words = re.findall(r'\b\w+\b', text.lower())
    return [word for word in words if word not in stop_words]

# Count word frequencies
def count_word_frequencies(words):
    return Counter(words)

def main():
    file_path = "random_pragraphs.txt"  # Update with the actual file path
    stop_words = ["the", "and", "in", "of", "to", "a", "is", "it", "that"]  # Customize your stop words

    text = read_file(file_path)
    if not text:
        return

    processed_words = remove_stop_words(text, stop_words)
    word_frequencies = count_word_frequencies(processed_words)

    print("Word Frequency Count:")
    for word, count in word_frequencies.items():
        print(f"{word}: {count}")

if __name__ == "__main__":
    main()
