/**
 * @param {String} HTML representing a single element.
 * @param {Boolean} flag representing whether or not to trim input whitespace, defaults to true.
 * @return {Element | HTMLCollection | null}
 */
function fromHTML(html, trim = true) {
    // Process the HTML string.
    html = trim ? html.trim() : html;
    if (!html) return null;

    // Then set up a new template element.
    const template = document.createElement('template');
    template.innerHTML = html;
    const result = template.content.children;

    // Then return either an HTMLElement or HTMLCollection,
    // based on whether the input HTML had one or more roots.
    if (result.length === 1) return result[0];
    return result;
}

/**
 * Convert bytes into human readable format
 * @param {number} size size in bytes
 * @return {string} human readable size
 */
function getFileSize(size) {
    var i = size == 0 ? 0 : Math.floor(Math.log(size) / Math.log(1024));
    return +((size / Math.pow(1024, i)).toFixed(2)) * 1 + ' ' + ['B', 'kB', 'MB', 'GB', 'TB'][i];
}

// #region file storage functions
function copyToClipboard(text) {
    navigator.clipboard.writeText(text)
    alert("Skopiowano do schowka.")
}

function browseFiles(url) {
    window.open(url, "_blank")
}

function selectFile(url, input_id) {
    if (window.opener) {
        window.opener.document.getElementById(input_id).value = url
        window.close()
    }
}

function initFileReplace(file_name) {
    // just trigger file upload and mention to keep the same name
    const fileInput = document.querySelector("input#files")
    const fileNameInput = document.querySelector("input[name='force_file_name']")

    fileInput.addEventListener("change", () => {
        if (!fileInput.files.length) {
            fileNameInput.value = undefined
            return
        }

        fileNameInput.value = file_name
        fileInput.form.submit()
    })

    fileInput.click()
}
// #endregion
