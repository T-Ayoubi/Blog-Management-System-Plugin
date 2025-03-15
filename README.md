# Blog Management System (BMS) Plugin

A **simple yet powerful Blog Management System** built as a WordPress plugin. It allows users to **view**, **add**, **edit**, and **delete** custom blog posts, along with displaying them anywhere on your WordPress site using shortcodes. The plugin is fully REST API integrated, with secure JWT authentication for protected actions.

---

## 🌟 Features

✅ Manage blog posts using **Custom Post Types (CPTs)**  
✅ **Custom REST API Endpoints** (GET, POST, PUT, DELETE)  
✅ **JWT Authentication** for secure API access  
✅ Custom fields for **Author**, **Category**, and **Featured Image**  
✅ **Shortcode** to display blog posts anywhere (`[bms_blog_list]`)  
✅ Pagination, post forms, and admin actions on the frontend  
✅ Supports **Advanced Custom Fields (ACF)** (optional, for more flexibility)  
✅ Role-based access: Only **Admins** can delete posts  
✅ Search & filtering by **Category**  
✅ Clean and responsive UI (CSS ready)

---

## 🚀 Installation

1. **Download** the latest plugin release as a `.zip` file from the [GitHub repository](#).
2. Go to your WordPress admin dashboard → `Plugins` → `Add New`.
3. Click `Upload Plugin` and upload the downloaded `.zip` file.
4. Install and activate the **Blog Management System (BMS)** plugin.

---

## ⚙️ Usage Guide

### 1. Display Blog Posts Anywhere
Use the shortcode (`[bms_blog_list]`)  to display the blog post listing, including pagination, edit/delete buttons, and an add post form for logged-in users:

You can place this shortcode on any page or post, and it will render the full blog management interface.

### 2. Registering the Custom Post Type Archive
To ensure that the archive page for the custom post type is registered correctly, please save the permalinks after activating the plugin. This will ensure that WordPress flushes the rewrite rules and registers the custom post type's archive URL.

To do this:
1. Go to **Settings** → **Permalinks** in your WordPress admin dashboard.
2. Simply click **Save Changes** without making any changes.

This step is crucial for proper functionality of the custom post type archive page.

### 3. API Endpoints (JWT Secured)

| Method | Endpoint                         | Description         |
|--------|----------------------------------|---------------------|
| GET    | `/wp-json/custom/v1/posts`       | Retrieve all posts  |
| POST   | `/wp-json/custom/v1/posts`       | Create a new post   |
| PUT    | `/wp-json/custom/v1/posts/{id}`  | Update a post       |
| DELETE | `/wp-json/custom/v1/posts/{id}`  | Delete a post       |

#### Important:
- **POST**, **PUT**, and **DELETE** requests **require a valid JWT token** for authentication.
- Users must **log in** and obtain a **JWT token** from your website in order to perform these actions.
  
👉 You can integrate any JWT plugin or custom implementation for user authentication and token generation.

---

## 🛡️ JWT Authentication Setup (Required for API access)

1. Install a JWT Authentication plugin (like [JWT Authentication for WP REST API](https://wordpress.org/plugins/jwt-authentication-for-wp-rest-api/)) or use your custom solution.
2. Follow the plugin setup instructions to enable token generation.
3. After obtaining the JWT token, include it in the `Authorization` header as follows:
   
   ```
   Authorization: Bearer YOUR_JWT_TOKEN
   ```

---

## 👥 User Roles & Permissions

- **Admins**: Full permissions (Add, Edit, Delete blog posts)
- **Logged-in Users**: Can add and edit posts (depending on role)
- **Guests**: Can only view posts

---

## 🔍 Search & Filtering 

- Users can search and filter blog posts by **Category** using the provided interface on the blog list page (via `[bms_blog_list]` shortcode).

---

## ✅ Requirements

- WordPress (latest version recommended)
- PHP 7.4+
- JWT Authentication (for secure API operations)

---

## 🖥️ Development & Deployment

- Tested on **LocalWP** and **XAMPP** environments.
- Ready for deployment on any live WordPress hosting (SiteGround, Bluehost, WP Engine, etc.).

---

## 📦 Project Structure

- `includes/class-bms-rest-api.php` → REST API logic  
- `includes/class-shortcodes.php` → Shortcode handling (`[bms_blog_list]`)  
- `includes/archive-bms_blog.php` → Blog listing and frontend forms  
- `includes/search-bms.php` → Search and filter functionality  

---

## 📚 Assumptions & Notes

- The plugin assumes JWT authentication is already configured and operational on the WordPress site.
- Advanced Custom Fields (ACF) can be used to extend the post metadata.

---

## 💡 Future Improvements (Optional)

- Support for comments on blog posts  

---

## 🙌 Contribution & Support

Feel free to fork the repository and submit pull requests.  
For any issues, please open an issue on the GitHub repository.

---

