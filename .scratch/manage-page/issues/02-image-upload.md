# 02: Image upload

**What to build:** Endpoint to upload an image, protected by `edit_page`. Accepts jpg/png/webp up to 5 MB (checked by real content type, not extension; svg rejected), stores it on a docker volume under a generated name, and returns its public URL. nginx serves `/uploads/*`. `PATCH` for type `image` accepts only URLs under `/uploads/` or `/img/`.

**Blocked by:** 01

**Status:** ready-for-agent

- [ ] Upload without Session gives 401, without `edit_page` gives 403
- [ ] Valid jpg/png/webp is stored and reachable at the returned URL through nginx
- [ ] svg, oversized file, or a non-image renamed to `.jpg` gives 400
- [ ] `PATCH` of an `image` value with an external URL is rejected, with an uploaded URL accepted
- [ ] Docker volume and nginx location configured for dev and test
- [ ] Cest covers success and each rejection
