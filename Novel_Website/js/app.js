// Static data for demonstration
const novels = [
    { id: 1, title: "The Adventures of Sherlock Holmes", author: "Arthur Conan Doyle", date: "14-18-1892", chapters: ["Chapter 1.1", "Chapter 1.2"] },
    { id: 2, title: "The Great Gatsby", author: "F.Scott Fitzegerald", date: "10-4-1925", chapters: ["Chapter 2.1", "Chapter 2.2"] },
    { id: 3, title: "Macbeth", author: "William Shakespear", date: 1600, chapters: ["Chapter 3.1", "Chapter 3.2"] },
    { id: 4, title: "The Poor Folk", author: "Fyodor Destovesky", date: 1800, chapters: ["Chapter 4.1", "Chapter 4.2"] },
    { id: 5, title: "Anna Kerenina", author: "Leo Tolstoy", date: 1828, chapters: ["Chapter 5.1", "Chapter 5.2"] },
    { id: 6, title: "Love in the Time of Cholera", author: "Gabriel Garcia Marquez", date: 1985, chapters: ["Chapter 6.1", "Chapter 6.2"] }
];

// Home Page: Populate Novel Table
if (document.querySelector("#novel-table")) {
    const novelTable = document.getElementById("novel-table");
    novels.forEach(novel => {
        const novelItem = document.createElement("div");
        novelItem.classList.add("novel-item");
        novelItem.innerHTML = `
            <a href="novel.html?id=${novel.id}"><img src="https://d28hgpri8am2if.cloudfront.net/book_images/onix/cvr9781524879761/the-great-gatsby-9781524879761_hr.jpg" alt="${novel.title}"></a>
            <h3><a href="novel.html?id=${novel.id}">${novel.title}</a></h3>
            <h4>${novel.author}</h4>
        `;
        novelTable.appendChild(novelItem);
    });
}

// Novel Page: Populate Novel Details
if (document.querySelector("#novel-title")) {
    const params = new URLSearchParams(window.location.search);
    const novelId = parseInt(params.get("id"));
    const novel = novels.find(n => n.id === novelId);
    if (novel) {
        document.getElementById("novel-title").textContent = novel.title;
        document.getElementById("novel-author").textContent += novel.author;
        document.getElementById("novel-date").textContent += novel.date;

        const chaptersList = document.getElementById("chapters-list");
        novel.chapters.forEach((chapter, index) => {
            const chapterItem = document.createElement("li");
            chapterItem.innerHTML = `<a href="chapter.html?id=${novel.id}&chapter=${index}">${chapter}</a>`;
            chaptersList.appendChild(chapterItem);
        });
    }
}

// Chapter Page: Populate Chapter Content
if (document.querySelector("#chapter-title")) {
    const params = new URLSearchParams(window.location.search);
    const novelId = parseInt(params.get("id"));
    const chapterIndex = parseInt(params.get("chapter"));
    const novel = novels.find(n => n.id === novelId);

    if (novel) {
        const chapterTitle = `Chapter ${chapterIndex + 1}`;
        const chapterContent = novel.chapters[chapterIndex];

        document.getElementById("chapter-title").textContent = chapterTitle;
        document.getElementById("chapter-content").textContent = chapterContent;

        document.getElementById("prev-chapter").onclick = () => {
            if (chapterIndex > 0) {
                window.location.href = `chapter.html?id=${novelId}&chapter=${chapterIndex - 1}`;
            }
        };

        document.getElementById("next-chapter").onclick = () => {
            if (chapterIndex < novel.chapters.length - 1) {
                window.location.href = `chapter.html?id=${novelId}&chapter=${chapterIndex + 1}`;
            }
        };
    }
}
