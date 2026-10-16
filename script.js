const resultsBody = document.getElementById('books-body');
const searchInput = document.getElementById('search');
const form = document.getElementById('add-form');
const formMessage = document.getElementById('form-message');

// Максимальний рік = поточний (серверна валідація теж це перевіряє)
document.getElementById('year').max = new Date().getFullYear();

// Лічильник запитів: якщо відповіді приходять не по порядку, показуємо лише найновішу
let requestCounter = 0;

function setPlaceholder(text, isError = false) {
    const tr = document.createElement('tr');
    tr.className = 'placeholder' + (isError ? ' error' : '');
    const td = document.createElement('td');
    td.colSpan = 6;
    td.textContent = text;
    tr.append(td);
    resultsBody.replaceChildren(tr);
}

function createRow(book) {
    const tr = document.createElement('tr');
    const available = Number(book.is_available) === 1;
    const values = [book.id, book.title, book.author, book.year, book.isbn, available ? 'Доступна' : 'Видана'];
    values.forEach(value => {
        const td = document.createElement('td');
        td.textContent = value; // textContent, а не innerHTML — захист від XSS
        tr.append(td);
    });
    return tr;
}

function renderBooks(books) {
    if (books.length === 0) {
        setPlaceholder('Нічого не знайдено');
        return;
    }
    resultsBody.replaceChildren(...books.map(createRow));
}

async function loadBooks(term = '') {
    const myRequest = ++requestCounter;
    try {
        const url = term === '' ? 'api_list.php' : 'api_list.php?q=' + encodeURIComponent(term);
        const response = await fetch(url);
        if (!response.ok) {
            throw new Error('сервер повернув помилку ' + response.status);
        }
        const books = await response.json();
        if (myRequest !== requestCounter) return; // є новіший запит
        renderBooks(books);
    } catch (error) {
        if (myRequest !== requestCounter) return;
        setPlaceholder('Не вдалося завантажити список: ' + error.message, true);
    }
}

// Додає рядок у кінець таблиці, якщо книга відповідає поточному пошуку.
// Повертає true, якщо рядок додано.
function appendBook(book) {
    const term = searchInput.value.trim().toLowerCase();
    if (!book.title.toLowerCase().includes(term)) {
        return false;
    }
    resultsBody.querySelector('.placeholder')?.remove();
    resultsBody.append(createRow(book));
    return true;
}

function showFormMessage(text, isError) {
    formMessage.textContent = text;
    formMessage.className = isError ? 'error' : 'success';
}

// Живий пошук
searchInput.addEventListener('input', () => {
    loadBooks(searchInput.value.trim());
});

// Додавання без перезавантаження сторінки
form.addEventListener('submit', async (event) => {
    event.preventDefault();
    formMessage.textContent = '';

    try {
        const response = await fetch('api_add.php', {
            method: 'POST',
            body: new FormData(form),
        });
        const result = await response.json().catch(() => null);

        if (!response.ok) {
            const details = result && result.errors
                ? Object.values(result.errors).join('; ')
                : (result && result.error) || 'помилка сервера ' + response.status;
            throw new Error(details);
        }

        const shown = appendBook(result);
        form.reset();
        showFormMessage(
            shown
                ? 'Книгу додано (id ' + result.id + ').'
                : 'Книгу додано (id ' + result.id + '), але вона не відповідає поточному пошуку.',
            false
        );
    } catch (error) {
        showFormMessage('Не вдалося додати книгу: ' + error.message, true);
    }
});

// Початкове завантаження списку
loadBooks();