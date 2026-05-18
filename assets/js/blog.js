let blogCurrentPage = 1;
let blogCurrentSearch = '';
let blogPostToDelete = null;

document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('postsGrid')) {
        loadBlogPosts();
        loadBlogStats();
        setupBlogFileUpload();
        
        const blogForm = document.getElementById('blogPostForm');
        if (blogForm) {
            blogForm.addEventListener('submit', handleBlogPostSubmit);
        }
        
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            let debounceTimer;
            searchInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    blogCurrentSearch = this.value.trim();
                    blogCurrentPage = 1;
                    loadBlogPosts();
                }, 500);
            });
        }
    }
});

function setupBlogFileUpload() {
    const uploadZone = document.getElementById('uploadZone');
    const imageInput = document.getElementById('coverImage');
    
    if (!uploadZone || !imageInput) return;
    
    uploadZone.addEventListener('click', function() {
        imageInput.click();
    });
    
    uploadZone.addEventListener('dragover', function(event) {
        event.preventDefault();
        uploadZone.style.borderColor = '#1B6CA8';
    });
    
    uploadZone.addEventListener('dragleave', function(event) {
        event.preventDefault();
        uploadZone.style.borderColor = '#DDE3EC';
    });
    
    uploadZone.addEventListener('drop', function(event) {
        event.preventDefault();
        
        if (event.dataTransfer.files.length > 0) {
            imageInput.files = event.dataTransfer.files;
            const spans = uploadZone.querySelectorAll('span');
            if (spans[0]) {
                spans[0].textContent = event.dataTransfer.files[0].name;
            }
        }
        uploadZone.style.borderColor = '#DDE3EC';
    });
    
    imageInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            const spans = uploadZone.querySelectorAll('span');
            if (spans[0]) {
                spans[0].textContent = this.files[0].name;
            }
        }
    });
}

function loadBlogPosts() {
    let url = `index.php?controller=blog&action=getPosts&page=${blogCurrentPage}`;
    if (blogCurrentSearch) {
        url += `&search=${encodeURIComponent(blogCurrentSearch)}`;
    }
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderBlogPosts(data.posts || []);
                renderBlogPagination(data.totalPages || 1, data.currentPage || 1);
                const postsCountSpan = document.getElementById('postsCount');
                if (postsCountSpan) {
                    postsCountSpan.textContent = `Showing ${data.totalPosts} of ${data.totalPosts}`;
                }
            }
        })
        .catch(error => console.error('Error loading posts:', error));
}

function loadBlogStats() {
    fetch('index.php?controller=blog&action=getStats')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const postCountElement = document.getElementById('postCount');
                const authorCountElement = document.getElementById('authorCount');
                const totalReadsElement = document.getElementById('totalReads');
                
                if (postCountElement) postCountElement.textContent = data.postCount;
                if (authorCountElement) authorCountElement.textContent = data.authorCount;
                if (totalReadsElement) totalReadsElement.textContent = data.totalReads.toLocaleString();
            }
        })
        .catch(error => console.error('Error loading stats:', error));
}

function renderBlogPosts(posts) {
    const postsGrid = document.getElementById('postsGrid');
    if (!postsGrid) return;
    
    if (posts.length === 0) {
        postsGrid.innerHTML = `
            <div class="blog-card" style="grid-column:1/-1; text-align:center; padding:40px;">
                <i class="ti ti-article" style="font-size:48px; color:#8A97A8;"></i>
                <p style="color:#4A5768; margin-top:16px;">No blog posts found. Be the first to share your experience!</p>
            </div>
        `;
        return;
    }
    
    postsGrid.innerHTML = '';
    posts.forEach(post => {
        postsGrid.appendChild(createBlogPostCard(post));
    });
}

function createBlogPostCard(post) {
    const card = document.createElement('div');
    card.className = 'blog-card clickable';
    card.style.cursor = 'pointer';
    card.onclick = () => window.location.href = `index.php?id=${post.id}`;
    
    let authorInitial = 'U';
    if (post.author_initial) {
        authorInitial = post.author_initial;
    } else if (post.author_name) {
        authorInitial = post.author_name.charAt(0) + post.author_name.charAt(post.author_name.length - 1);
    }
    
    const adminBadge = (post.author_role === 'admin') ? '<span class="admin-badge">Admin</span>' : '';
    
    let imageHtml = '';
    if (post.image_path && post.image_path !== 'null') {
        imageHtml = `<img src="/online-car-rent-management-system/${post.image_path}" alt="Post image" style="width:100%; height:100%; object-fit:cover;">`;
    } else {
        imageHtml = `
            <div class="card-img-placeholder">
                <i class="ti ti-mountain"></i>
                <span>Travel Photo</span>
            </div>
        `;
    }
    
    let deleteButton = '';
    if (post.can_delete) {
        deleteButton = `
            <div class="card-actions">
                <button class="btn-del" onclick="event.stopPropagation(); window.deletePost(${post.id})">
                    <i class="ti ti-trash"></i> Delete
                </button>
            </div>
        `;
    }
    
    card.innerHTML = `
        <div class="card-img-wrap">${imageHtml}</div>
        <div class="card-title">${escapeHtml(post.title)}</div>
        <div class="card-excerpt">${escapeHtml(post.content)}</div>
        <div class="card-meta">
            <div class="card-author">
                <div class="author-avatar">${escapeHtml(authorInitial)}</div>
                <span class="author-name">${escapeHtml(post.author_name)}</span>
                ${adminBadge}
            </div>
            <span class="card-date">${escapeHtml(post.created_at)}</span>
        </div>
        ${deleteButton}
    `;
    
    return card;
}

function renderBlogPagination(totalPages, currentPageNumber) {
    const paginationContainer = document.getElementById('pagination');
    if (!paginationContainer) return;
    
    if (totalPages <= 1) {
        paginationContainer.innerHTML = '';
        return;
    }
    
    let htmlString = '';
    
    if (currentPageNumber > 1) {
        htmlString += `<button class="pg-btn" onclick="goToBlogPage(${currentPageNumber - 1})"><i class="ti ti-chevron-left"></i></button>`;
    }
    
    const startPage = Math.max(1, currentPageNumber - 2);
    const endPage = Math.min(totalPages, startPage + 4);
    
    for (let i = startPage; i <= endPage; i++) {
        const activeClass = (i === currentPageNumber) ? 'active' : '';
        htmlString += `<button class="pg-btn ${activeClass}" onclick="goToBlogPage(${i})">${i}</button>`;
    }
    
    if (currentPageNumber < totalPages) {
        htmlString += `<button class="pg-btn" onclick="goToBlogPage(${currentPageNumber + 1})"><i class="ti ti-chevron-right"></i></button>`;
    }
    
    paginationContainer.innerHTML = htmlString;
}

function goToBlogPage(pageNumber) {
    blogCurrentPage = pageNumber;
    loadBlogPosts();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function handleBlogPostSubmit(event) {
    event.preventDefault();
    
    const titleInput = document.getElementById('postTitle');
    const contentTextarea = document.getElementById('postContent');
    const imageFileInput = document.getElementById('coverImage');
    
    const title = titleInput.value.trim();
    const content = contentTextarea.value.trim();
    const imageFile = imageFileInput.files[0];
    
    if (!title) {
        alert('Title is required');
        return;
    }
    if (title.length < 5) {
        alert('Title must be at least 5 characters');
        return;
    }
    if (!content) {
        alert('Content is required');
        return;
    }
    if (content.length < 20) {
        alert('Content must be at least 20 characters');
        return;
    }
    
    const submitButton = event.target.querySelector('.form-submit');
    const originalButtonText = submitButton.innerHTML;
    submitButton.innerHTML = '<i class="ti ti-loader"></i> Publishing...';
    submitButton.disabled = true;
    
    const formData = new FormData();
    formData.append('action', 'create');
    formData.append('title', title);
    formData.append('content', content);
    if (imageFile) {
        formData.append('cover_image', imageFile);
    }
    
    fetch('index.php?controller=blog&action=create', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Post created successfully!');
            document.getElementById('blogPostForm').reset();
            resetBlogUploadZone();
            blogCurrentSearch = '';
            blogCurrentPage = 1;
            const searchInput = document.getElementById('searchInput');
            if (searchInput) searchInput.value = '';
            loadBlogPosts();
            loadBlogStats();
        } else {
            let errorMessage = 'Failed to create post';
            if (data.errors) {
                errorMessage = Object.values(data.errors).join('\n');
            } else if (data.message) {
                errorMessage = data.message;
            }
            alert(errorMessage);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    })
    .finally(() => {
        submitButton.innerHTML = originalButtonText;
        submitButton.disabled = false;
    });
}

function resetBlogUploadZone() {
    const uploadZone = document.getElementById('uploadZone');
    if (uploadZone) {
        const spans = uploadZone.querySelectorAll('span');
        if (spans[0]) {
            spans[0].textContent = 'Click or drag & drop to upload';
            spans[0].style.fontWeight = 'normal';
        }
    }
    const imageInput = document.getElementById('coverImage');
    if (imageInput) imageInput.value = '';
}

window.deletePost = function(postId) {
    const confirmMessage = 'Are you sure you want to delete this post?';
    
    if (confirm(confirmMessage)) {
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('post_id', postId);
        
        fetch('index.php?controller=blog&action=delete', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Post deleted successfully!');
                const isDetailsPage = window.location.href.indexOf('id=') > -1;
                if (isDetailsPage) {
                    window.location.href = 'index.php';
                } else {
                    loadBlogPosts();
                    loadBlogStats();
                }
            } else {
                alert(data.message || 'Failed to delete post');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the post');
        });
    }
};

function searchByTag(tag) {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.value = tag;
        blogCurrentSearch = tag;
        blogCurrentPage = 1;
        loadBlogPosts();
    }
}

function closeModal(modalId) {
    const modalElement = document.getElementById(modalId);
    if (modalElement) {
        modalElement.classList.remove('active');
    }
}

function showModal(modalId) {
    const modalElement = document.getElementById(modalId);
    if (modalElement) {
        modalElement.classList.add('active');
    }
}

function escapeHtml(text) {
    if (!text) return '';
    return text.replace(/[&<>]/g, function(match) {
        if (match === '&') return '&amp;';
        if (match === '<') return '&lt;';
        if (match === '>') return '&gt;';
        return match;
    });
}let currentPage = 1;
let currentSearch = '';
let postToDelete = null;

document.addEventListener('DOMContentLoaded', function() {
    // Only run on index page (posts grid exists)
    if (document.getElementById('postsGrid')) {
        loadPosts();
        loadStats();
        setupFileUpload();
        
        const blogForm = document.getElementById('blogPostForm');
        if (blogForm) {
            blogForm.addEventListener('submit', handlePostSubmit);
        }
        
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            let debounceTimer;
            searchInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    currentSearch = this.value.trim();
                    currentPage = 1;
                    loadPosts();
                }, 500);
            });
        }
    }
});

function setupFileUpload() {
    const uploadZone = document.getElementById('uploadZone');
    const imageInput = document.getElementById('coverImage');
    
    if (!uploadZone || !imageInput) return;
    
    // Click on the zone to trigger file input
    uploadZone.addEventListener('click', function() {
        imageInput.click();
    });
    
    // Handle drag over event
    uploadZone.addEventListener('dragover', function(event) {
        event.preventDefault();
        uploadZone.style.borderColor = '#1B6CA8';
    });
    
    // Handle drag leave event
    uploadZone.addEventListener('dragleave', function(event) {
        event.preventDefault();
        uploadZone.style.borderColor = '#DDE3EC';
    });
    
    // Handle drop event
    uploadZone.addEventListener('drop', function(event) {
        event.preventDefault();
        
        if (event.dataTransfer.files.length > 0) {
            imageInput.files = event.dataTransfer.files;
            
            const spans = uploadZone.querySelectorAll('span');
            if (spans[0]) {
                spans[0].textContent = event.dataTransfer.files[0].name;
            }
        }
        
        uploadZone.style.borderColor = '#DDE3EC';
    });
    
    // Handle file selection via input
    imageInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            const spans = uploadZone.querySelectorAll('span');
            if (spans[0]) {
                spans[0].textContent = this.files[0].name;
            }
        }
    });
}

function loadPosts() {
    let url = `index.php?controller=blog&action=getPosts&page=${currentPage}`;
    if (currentSearch) {
        url += `&search=${encodeURIComponent(currentSearch)}`;
    }
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderPosts(data.posts || []);
                renderPagination(data.totalPages || 1, data.currentPage || 1);
                
                const postsCountSpan = document.getElementById('postsCount');
                if (postsCountSpan) {
                    postsCountSpan.textContent = `Showing ${data.totalPosts} of ${data.totalPosts}`;
                }
            }
        })
        .catch(error => console.error('Error loading posts:', error));
}

function loadStats() {
    fetch('index.php?controller=blog&action=getStats')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const postCountElement = document.getElementById('postCount');
                const authorCountElement = document.getElementById('authorCount');
                const totalReadsElement = document.getElementById('totalReads');
                
                if (postCountElement) postCountElement.textContent = data.postCount;
                if (authorCountElement) authorCountElement.textContent = data.authorCount;
                if (totalReadsElement) totalReadsElement.textContent = data.totalReads.toLocaleString();
            }
        })
        .catch(error => console.error('Error loading stats:', error));
}

function renderPosts(posts) {
    const postsGrid = document.getElementById('postsGrid');
    if (!postsGrid) return;
    
    if (posts.length === 0) {
        postsGrid.innerHTML = `
            <div class="blog-card" style="grid-column:1/-1; text-align:center; padding:40px;">
                <i class="ti ti-article" style="font-size:48px; color:#8A97A8;"></i>
                <p style="color:#4A5768; margin-top:16px;">No blog posts found. Be the first to share your experience!</p>
            </div>
        `;
        return;
    }
    
    postsGrid.innerHTML = '';
    
    for (let i = 0; i < posts.length; i++) {
        const post = posts[i];
        const postCard = createPostCard(post);
        postsGrid.appendChild(postCard);
    }
}

function createPostCard(post) {
    const card = document.createElement('div');
    card.className = 'blog-card clickable';
    card.style.cursor = 'pointer';
    
    card.onclick = function() {
        window.location.href = `index.php?id=${post.id}`;
    };
    
    // Get author initial
    let authorInitial = 'U';
    if (post.author_initial) {
        authorInitial = post.author_initial;
    } else if (post.author_name) {
        authorInitial = post.author_name.charAt(0) + post.author_name.charAt(post.author_name.length - 1);
    }
    
    // Admin badge
    const adminBadge = (post.author_role === 'admin') ? '<span class="admin-badge">Admin</span>' : '';
    
    // Image HTML
    let imageHtml = '';
    if (post.image_path && post.image_path !== 'null') {
        imageHtml = `<img src="/Web-Technologies/Project/Online-Car-Rent/${post.image_path}" alt="Post image" style="width:100%; height:100%; object-fit:cover;">`;
    } else {
        imageHtml = `
            <div class="card-img-placeholder">
                <i class="ti ti-mountain"></i>
                <span>Travel Photo</span>
            </div>
        `;
    }
    
    // Delete button (only if user can delete)
    let deleteButton = '';
    if (post.can_delete) {
        deleteButton = `
            <div class="card-actions">
                <button class="btn-del" onclick="event.stopPropagation(); window.deletePost(${post.id})">
                    <i class="ti ti-trash"></i> Delete
                </button>
            </div>
        `;
    }
    
    card.innerHTML = `
        <div class="card-img-wrap">${imageHtml}</div>
        <div class="card-title">${escapeHtml(post.title)}</div>
        <div class="card-excerpt">${escapeHtml(post.content)}</div>
        <div class="card-meta">
            <div class="card-author">
                <div class="author-avatar">${escapeHtml(authorInitial)}</div>
                <span class="author-name">${escapeHtml(post.author_name)}</span>
                ${adminBadge}
            </div>
            <span class="card-date">${escapeHtml(post.created_at)}</span>
        </div>
        ${deleteButton}
    `;
    
    return card;
}

function renderPagination(totalPages, currentPageNumber) {
    const paginationContainer = document.getElementById('pagination');
    if (!paginationContainer) return;
    
    if (totalPages <= 1) {
        paginationContainer.innerHTML = '';
        return;
    }
    
    let htmlString = '';
    
    // Previous button
    if (currentPageNumber > 1) {
        htmlString += `<button class="pg-btn" onclick="goToPage(${currentPageNumber - 1})"><i class="ti ti-chevron-left"></i></button>`;
    }
    
    // Page numbers
    const startPage = Math.max(1, currentPageNumber - 2);
    const endPage = Math.min(totalPages, startPage + 4);
    
    for (let i = startPage; i <= endPage; i++) {
        const activeClass = (i === currentPageNumber) ? 'active' : '';
        htmlString += `<button class="pg-btn ${activeClass}" onclick="goToPage(${i})">${i}</button>`;
    }
    
    // Next button
    if (currentPageNumber < totalPages) {
        htmlString += `<button class="pg-btn" onclick="goToPage(${currentPageNumber + 1})"><i class="ti ti-chevron-right"></i></button>`;
    }
    
    paginationContainer.innerHTML = htmlString;
}

function goToPage(pageNumber) {
    currentPage = pageNumber;
    loadPosts();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function handlePostSubmit(event) {
    event.preventDefault();
    
    const titleInput = document.getElementById('postTitle');
    const contentTextarea = document.getElementById('postContent');
    const imageFileInput = document.getElementById('coverImage');
    
    const title = titleInput.value.trim();
    const content = contentTextarea.value.trim();
    const imageFile = imageFileInput.files[0];
    
    // Validation
    if (!title) {
        alert('Title is required');
        return;
    }
    
    if (title.length < 5) {
        alert('Title must be at least 5 characters');
        return;
    }
    
    if (!content) {
        alert('Content is required');
        return;
    }
    
    if (content.length < 20) {
        alert('Content must be at least 20 characters');
        return;
    }
    
    const submitButton = event.target.querySelector('.form-submit');
    const originalButtonText = submitButton.innerHTML;
    submitButton.innerHTML = '<i class="ti ti-loader"></i> Publishing...';
    submitButton.disabled = true;
    
    const formData = new FormData();
    formData.append('action', 'create');
    formData.append('title', title);
    formData.append('content', content);
    
    if (imageFile) {
        formData.append('cover_image', imageFile);
    }
    
    fetch('index.php?controller=blog&action=create', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Post created successfully!');
            
            // Reset form
            document.getElementById('blogPostForm').reset();
            resetUploadZone();
            
            // Reset search
            currentSearch = '';
            currentPage = 1;
            
            const searchInput = document.getElementById('searchInput');
            if (searchInput) searchInput.value = '';
            
            // Reload posts and stats
            loadPosts();
            loadStats();
        } else {
            let errorMessage = 'Failed to create post';
            if (data.errors) {
                errorMessage = Object.values(data.errors).join('\n');
            } else if (data.message) {
                errorMessage = data.message;
            }
            alert(errorMessage);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    })
    .finally(() => {
        submitButton.innerHTML = originalButtonText;
        submitButton.disabled = false;
    });
}

function resetUploadZone() {
    const uploadZone = document.getElementById('uploadZone');
    if (uploadZone) {
        const spans = uploadZone.querySelectorAll('span');
        if (spans[0]) {
            spans[0].textContent = 'Click or drag & drop to upload';
            spans[0].style.fontWeight = 'normal';
        }
    }
    
    const imageInput = document.getElementById('coverImage');
    if (imageInput) imageInput.value = '';
}

// Global deletePost function for both index and details page
window.deletePost = function(postId) {
    const confirmMessage = 'Are you sure you want to delete this post?';
    
    if (confirm(confirmMessage)) {
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('post_id', postId);
        
        fetch('index.php?controller=blog&action=delete', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Post deleted successfully!');
                
                // Check if we're on details page or index page
                const isDetailsPage = window.location.href.indexOf('id=') > -1;
                
                if (isDetailsPage) {
                    window.location.href = 'index.php';
                } else {
                    loadPosts();
                    loadStats();
                }
            } else {
                alert(data.message || 'Failed to delete post');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the post');
        });
    }
};

function searchByTag(tag) {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.value = tag;
        currentSearch = tag;
        currentPage = 1;
        loadPosts();
    }
}

function closeModal(modalId) {
    const modalElement = document.getElementById(modalId);
    if (modalElement) {
        modalElement.classList.remove('active');
    }
}

function escapeHtml(text) {
    if (!text) return '';
    
    return text.replace(/[&<>]/g, function(match) {
        if (match === '&') return '&amp;';
        if (match === '<') return '&lt;';
        if (match === '>') return '&gt;';
        return match;
    });
}