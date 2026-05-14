<?php include_once __DIR__ . '/../layouts/header.php'; ?>

<?php
// Set default values if variables are not defined (prevents errors)
if (!isset($posts)) $posts = [];
if (!isset($totalPosts)) $totalPosts = 0;
if (!isset($totalPages)) $totalPages = 1;
if (!isset($page)) $page = 1;
if (!isset($postCount)) $postCount = 0;
if (!isset($authorCount)) $authorCount = 0;
if (!isset($totalReads)) $totalReads = 0;
if (!isset($popularPosts)) $popularPosts = [];
?>

<div class="blog-wrap">

  <!-- REMOVED DUPLICATE NAVIGATION HERE -->

  <div class="blog-hero">
    <div class="blog-hero-label">Community Stories</div>
    <h1>ROAD DIARIES</h1>
    <p>Real experiences from real renters — tips, destinations, and everything in between.</p>
  </div>

  <div class="filter-bar">
    <button class="filter-pill active" data-category="">All Posts</button>
    <div class="filter-search">
      <i class="ti ti-search" aria-hidden="true"></i>
      <input type="text" id="searchInput" placeholder="Search posts...">
    </div>
  </div>

  <div style="padding: 1.25rem 2rem 0; display: grid; grid-template-columns: 1fr 280px; gap: 0;">
    <div class="stats-row">
      <div class="stat-box">
        <div class="stat-num" id="postCount"><?php echo $postCount; ?></div>
        <div class="stat-label">Posts</div>
      </div>
      <div class="stat-box">
        <div class="stat-num" id="authorCount"><?php echo $authorCount; ?></div>
        <div class="stat-label">Authors</div>
      </div>
      <div class="stat-box">
        <div class="stat-num" id="totalReads"><?php echo number_format($totalReads); ?></div>
        <div class="stat-label">Reads</div>
      </div>
    </div>
    <div></div>
  </div>

  <div class="blog-body">

    <div class="posts-section">
      <div class="posts-header">
        <h2>Latest Experiences</h2>
        <span class="posts-count" id="postsCount">Showing <?php echo count($posts); ?> of <?php echo $totalPosts; ?></span>
      </div>

      <div class="posts-grid" id="postsGrid">
        <?php if (empty($posts)): ?>
          <div class="blog-card" style="grid-column: 1/-1; text-align: center; padding: 40px;">
            <i class="ti ti-article" style="font-size: 48px; color: var(--text-lo); margin-bottom: 16px;"></i>
            <p style="color: var(--text-mid);">No blog posts found. Be the first to share your experience!</p>
          </div>
        <?php else: ?>
          <?php foreach ($posts as $post): ?>
            <div class="blog-card clickable" data-post-id="<?php echo $post['id']; ?>" onclick="window.location.href='index.php?id=<?php echo $post['id']; ?>'">
              <div class="card-img-wrap">
                <?php if (!empty($post['image_path']) && file_exists('../../' . $post['image_path'])): ?>
                  <img src="/Web-Technologies/Project/Online-Car-Rent/<?php echo $post['image_path']; ?>" alt="Post image" style="width: 100%; height: 100%; object-fit: cover;">
                <?php else: ?>
                  <div class="card-img-placeholder">
                    <i class="ti ti-mountain" aria-hidden="true"></i>
                    <span>Travel Photo</span>
                  </div>
                <?php endif; ?>
              </div>
              <div class="card-title"><?php echo htmlspecialchars($post['title']); ?></div>
              <div class="card-excerpt"><?php echo htmlspecialchars(substr($post['content'], 0, 120)) . '...'; ?></div>
              <div class="card-meta">
                <div class="card-author">
                  <div class="author-avatar"><?php echo substr($post['author_name'], 0, 1) . substr($post['author_name'], -1, 1); ?></div>
                  <span class="author-name"><?php echo htmlspecialchars($post['author_name']); ?></span>
                  <?php if (isset($post['author_role']) && $post['author_role'] === 'admin'): ?>
                    <span class="admin-badge">Admin</span>
                  <?php endif; ?>
                </div>
                <span class="card-date"><?php echo date('M j, Y', strtotime($post['created_at'])); ?></span>
              </div>
              <?php 
              $canDelete = (isset($_SESSION['user_id']) && isset($_SESSION['user_role']) && ($_SESSION['user_role'] === 'admin' || (isset($post['user_id']) && $_SESSION['user_id'] == $post['user_id'])));
              if ($canDelete): 
              ?>
              <div class="card-actions">
                <button class="btn-del" onclick="event.stopPropagation(); deletePost(<?php echo $post['id']; ?>)"><i class="ti ti-trash" aria-hidden="true"></i> Delete</button>
              </div>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

      <?php if ($totalPages > 1): ?>
      <div class="pagination" id="pagination">
        <?php for ($i = 1; $i <= min($totalPages, 5); $i++): ?>
          <button class="pg-btn <?php echo $i === $page ? 'active' : ''; ?>" onclick="goToPage(<?php echo $i; ?>)"><?php echo $i; ?></button>
        <?php endfor; ?>
        <?php if ($totalPages > 5): ?>
          <button class="pg-btn">…</button>
          <button class="pg-btn" onclick="goToPage(<?php echo $totalPages; ?>)"><?php echo $totalPages; ?></button>
        <?php endif; ?>
        <button class="pg-btn" onclick="goToPage(<?php echo $page + 1; ?>)"><i class="ti ti-chevron-right"></i></button>
      </div>
      <?php endif; ?>
    </div>

    <div class="sidebar">

      <?php if (isset($_SESSION['user_id'])): ?>
      <div class="post-form-card">
        <div class="form-header">
          <i class="ti ti-pencil-plus" aria-hidden="true"></i>
          <h3>Share Your Experience</h3>
        </div>

        <form id="blogPostForm" enctype="multipart/form-data">
          <div class="form-field">
            <label class="form-label">Post Title</label>
            <input class="form-input" type="text" id="postTitle" name="title" placeholder="e.g. My weekend trip to Sylhet...">
            <div class="error-message" id="titleError"></div>
          </div>

          <div class="form-field">
            <label class="form-label">Your Story</label>
            <textarea class="form-input form-textarea" id="postContent" name="content" placeholder="Tell others about your rental experience..."></textarea>
            <div class="error-message" id="contentError"></div>
          </div>

          <div class="form-field">
            <label class="form-label">Cover Image (Optional)</label>
            <div class="img-upload-zone" id="uploadZone">
              <i class="ti ti-photo-plus" aria-hidden="true"></i>
              <span>Click or drag & drop to upload</span>
              <span class="upload-hint">JPG, PNG, WEBP, GIF — max 5MB</span>
              <input type="file" id="coverImage" name="cover_image" style="display: none;" accept="image/jpeg,image/png,image/webp,image/gif">
            </div>
            <div class="error-message" id="imageError"></div>
          </div>

          <button type="submit" class="form-submit">
            <i class="ti ti-send" aria-hidden="true"></i>
            Publish Post
          </button>
        </form>
      </div>
      <?php else: ?>
      <div class="post-form-card">
        <div class="form-header">
          <i class="ti ti-lock" aria-hidden="true"></i>
          <h3>Login to Share Your Experience</h3>
        </div>
        <p style="font-size: 12px; color: var(--text-mid); text-align: center;">Please login or sign up to create blog posts and share your road trip stories with the community.</p>
        <div style="display: flex; gap: 10px; margin-top: 15px;">
          <a href="../../views/auth/login.php" class="form-submit" style="flex: 1; text-align: center; text-decoration: none;">Login</a>
          <a href="../../views/auth/register.php" class="form-submit" style="flex: 1; text-align: center; text-decoration: none; background: var(--accent);">Sign Up</a>
        </div>
      </div>
      <?php endif; ?>

      <div class="sidebar-widget">
        <div class="widget-title">Trending This Week</div>
        <div id="popularPosts">
          <?php if (!empty($popularPosts)): ?>
            <?php foreach ($popularPosts as $index => $post): ?>
            <div class="popular-item">
              <div class="pop-num"><?php echo str_pad($index + 1, 2, '0', STR_PAD_LEFT); ?></div>
              <div class="pop-info">
                <div class="pop-title"><?php echo htmlspecialchars($post['title']); ?></div>
              </div>
            </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="popular-item">
              <div class="pop-info">
                <div class="pop-title">No posts yet</div>
                <div class="pop-meta">Be the first to post!</div>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <div class="sidebar-widget">
        <div class="widget-title">Topics</div>
        <div class="tag-cloud">
          <span class="tag" onclick="searchByTag('Road Trip')">Road Trip</span>
          <span class="tag" onclick="searchByTag('Adventure')">Adventure</span>
          <span class="tag" onclick="searchByTag('Family')">Family</span>
          <span class="tag" onclick="searchByTag('Budget Tips')">Budget Tips</span>
          <span class="tag" onclick="searchByTag('Luxury')">Luxury</span>
          <span class="tag" onclick="searchByTag('Safety')">Safety</span>
          <span class="tag" onclick="searchByTag('Night Drive')">Night Drive</span>
          <span class="tag" onclick="searchByTag('Hill Tracks')">Hill Tracks</span>
          <span class="tag" onclick="searchByTag('Beach')">Beach</span>
          <span class="tag" onclick="searchByTag('City Tour')">City Tour</span>
        </div>
      </div>

    </div>
  </div>
</div>

<div id="deleteConfirmModal" class="modal">
  <div class="modal-content">
    <h3>Delete Post</h3>
    <p>Are you sure you want to delete this post? This action cannot be undone.</p>
    <div class="modal-buttons">
      <button class="modal-confirm" id="confirmDeleteBtn">Delete</button>
      <button class="modal-cancel" onclick="closeModal('deleteConfirmModal')">Cancel</button>
    </div>
  </div>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>