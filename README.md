# TELKblog
A simple Laravel PHP blog using Markdown

# How to use
Fork this repository and run ``composer install`` to install dependencies.
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

# Dependencies
 * [Markdown Parser in PHP](https://parsedown.org/)
 * [prismjs](https://prismjs.com/)
