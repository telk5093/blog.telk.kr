# TELKblog
A simple small PHP blog using Markdown

# How to use
Make a markdown file(`*.md`) in `./posts` folder like this:  
```markdown
---
layout: post
title: "제목"
tags: [tag1, tag2]
comments: true
published: true
use_math: false
---

Post contents
```
If you add ``permalink: /some_path`` in two ---s(I'll call it as _meta_ data section), you may also access via `/some_path`.
<br />

If you need a custom page, then make a markdown file in `./pages` folder.  
And add a link to `./includes/header.php`, `./includes/footer.php` or whatever.

# Todo
 * Tags
 * Comment on/off
