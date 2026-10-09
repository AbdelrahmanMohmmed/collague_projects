const addNovelBtn = document.getElementById("add-novel-btn");
const addNovelForm = document.getElementById("add-novel-form");
const cancelFormBtn = document.getElementById("cancel-form-btn");
const novelList = document.getElementById("novel-list");

addNovelBtn.addEventListener("click", () => {
  addNovelForm.classList.remove("hidden");
});

cancelFormBtn.addEventListener("click", () => {
  addNovelForm.classList.add("hidden");
});

addNovelForm.addEventListener("submit", (event) => {
  event.preventDefault();
  const novelName = document.getElementById("novel-name").value;
  const novelAuthor = document.getElementById("novel-author").value;
  const novelDescription = document.getElementById("novel-description").value;
  const novelCover = document.getElementById("novel-picture").value;
  const novel = {title: novelName, author: novel.Author, description: novelDescription, cover: novelCover}

  // Add novel to the list
  const novelItem = document.createElement("div");
  novelItem.classList.add("novel-item");
  // novelItem.innerHTML = `
  //   <span>${novelName} by ${novelAuthor}</span>
  //   <div>
  //     <button class="edit-btn">Edit</button>
  //     <button class="remove-btn">Remove</button>
  //   </div>
  // `;

  novelItem.innerHTML = `
    <a href=""><img src="${novel.cover}" alt="${novel.title}"></a>
    <h3><a href="">${novel.title}</a></h3>
    <h4>${novel.author}</h4>
  `;


  // Add event listeners for edit and remove buttons
  novelItem.querySelector(".remove-btn").addEventListener("click", () => {
    novelItem.remove();
  });

  novelList.appendChild(novelItem);
  addNovelForm.classList.add("hidden");
  addNovelForm.reset();
});