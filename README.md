<h1 align="center">📆 PEER REVIEW GUIDE</h1>
<p align="center">A web-based system for structured peer review submissions, feedback tracking, image proofs, and validation workflows.</p>

<hr>

<h2>📌 Features</h2>

<h3>✅ Submission System</h3>
<ul>
  <li>Task name, reviewer, builder details</li>
  <li>Dynamic review questions (loaded from DB)</li>
  <li>Fatality flags and reviewer remarks</li>
  <li>Image uploads per question</li>
  <li>Confirmation modal before submitting</li>
</ul>

<h3>🗂️ Admin Feedback Panel</h3>
<ul>
  <li>Bootstrap-styled interface</li>
  <li>View all PR submissions with filters</li>
  <li>Detailed view page for each PRID</li>
  <li>Image preview modal viewer</li>
  <li>Status updates:
      <ul>
        <li><strong>Completed – Valid</strong></li>
        <li><strong>Completed – Invalid</strong></li>
        <li><strong>Pending – Builder Notified</strong></li>
      </ul>
  </li>
</ul>

<h3>📨 Email Notifications</h3>
<ul>
  <li>PHPMailer used for sending builder notifications</li>
  <li>AJAX-triggered send email button</li>
  <li>Automatically updates PR status</li>
</ul>

<h3>🔍 Filtering & Searching</h3>
<ul>
  <li>Search by PRID, Builder, or date</li>
  <li>Status filter</li>
  <li>Toggleable filter/search UI</li>
</ul>

<h3>🖼️ Image Proofs</h3>
<ul>
  <li>Per-question image uploads</li>
  <li>Thumbnails + modal preview</li>
</ul>

<hr>

<h2>🏗️ System Architecture</h2>

<h3>Backend</h3>
<ul>
  <li><strong>PHP</strong> for logic and DB communication</li>
  <li><strong>MySQL</strong> hosted on InfinityFree</li>
</ul>

<h3>Frontend</h3>
<ul>
  <li>HTML, CSS, JavaScript</li>
  <li>Bootstrap 5 and Bootstrap Icons</li>
  <li>SweetAlert2 for alerts</li>
</ul>

<h3>Email Service</h3>
<ul>
  <li>PHPMailer (included in the repository)</li>
</ul>

<hr>

<h2>📁 Folder Structure</h2>

<h2>📁 Folder Structure</h2>

<pre>
PEERREVIEWGUIDE
│
├── PHPMailer-master/
│   └── src/
│       ├── DSNConfigurator.php
│       ├── Exception.php
│       ├── OAuth.php
│       ├── OAuthTokenProvider.php
│       ├── PHPMailer.php
│       ├── POP3.php
│       └── SMTP.php
│
├── img/                      → Icons and visual assets
│
├── pr-feedback/              → Admin review & feedback module
│   ├── accept_review.php
│   ├── email_debug.log
│   ├── pr_feedback.css
│   ├── pr_feedback.php
│   ├── send_email.php
│   └── update_status.php
│
├── README.md                 → Documentation
│
├── composer.json             → Composer config for PHPMailer
├── composer.lock
│
├── index.html                → Peer Review submission interface
│
├── pr-css.css                → Global styles for forms
│
├── pr-js.js                  → Main JavaScript logic
│
└── submit_review.php         → Handles form submission logic
</pre>


<hr>

<h2>🛠️ Installation & Setup</h2>

<h3>1️⃣ Upload the Project</h3>
<p>Upload all files to your hosting provider (InfinityFree, XAMPP, etc.).</p>

<h3>2️⃣ Setup the Database</h3>
<p>Create tables:</p>

<h4><code>pr_submissions</code></h4>
<ul>
  <li>pr_id (PK)</li>
  <li>task_name</li>
  <li>peer_reviewer_name</li>
  <li>builder_name</li>
  <li>status</li>
  <li>answers (JSON)</li>
  <li>image_paths (JSON)</li>
  <li>submission_date (datetime)</li>
</ul>

<h4><code>questions</code></h4>
<ul>
  <li>question_id</li>
  <li>question_text</li>
</ul>

<h3>3️⃣ Verify Configuration</h3>
<p>Tools and technologies used in this project:</p>

<ul>
  <li><strong>PHP</strong> – Backend logic and server-side processing</li>
  <li><strong>MySQL</strong> – Database storage for submissions and questions</li>
  <li><strong>PHPMailer</strong> – Email sending for builder notifications</li>
  <li><strong>HTML5</strong> – Form structure and page layout</li>
  <li><strong>CSS3</strong> – Custom styles and interface design</li>
  <li><strong>JavaScript</strong> – Dynamic UI, AJAX requests, modal handling</li>
  <li><strong>Bootstrap 5</strong> – Layout, styling, and responsive design</li>
  <li><strong>SweetAlert2</strong> – Clean alert and confirmation dialogs</li>
  <li><strong>Bootstrap Icons</strong> – Icons for UI elements</li>
</ul>

