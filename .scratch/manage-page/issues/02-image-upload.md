# 02: Image upload

**What to build:** Endpoint to upload an image, protected by `edit_page`. Accepts jpg/png/webp up to 5 MB (checked by real content type, not extension; svg rejected), stores it on a docker volume under a generated name, and returns its public URL. nginx serves `/uploads/*`. `PATCH` for type `image` accepts only URLs under `/uploads/` or `/img/`.

**Blocked by:** 01

**Status:** resolved

- [x] Upload without Session gives 401, without `edit_page` gives 403
- [x] Valid jpg/png/webp is stored and reachable at the returned URL through nginx
- [x] svg, oversized file, or a non-image renamed to `.jpg` gives 400
- [x] `PATCH` of an `image` value with an external URL is rejected, with an uploaded URL accepted
- [x] Docker volume and nginx location configured for dev and test
- [x] Cest covers success and each rejection

## Notes

- `POST /api/v1/uploads/image` (multipart field `file`, `edit_page`) returns `{url: "/uploads/<uuid7>.<jpg|png|webp>"}` with 201.
- Format is detected from file content (`getimagesizefromstring`), never from the client's name or type. Limit 5 MB, checked in the app; `docker/php/uploads.ini` and nginx `client_max_body_size` leave headroom.
- Files live in `backend/storage/uploads` (bind-mounted into php read-write and nginx read-only, gitignored); `UPLOAD_DIR` overrides the path, tests use `runtime/test-uploads`.
- `PATCH` with `type: image` accepts only `/uploads/...` and `/img/...` paths (no `..`, no query, no host).
- The php image was rebuilt for the new ini: run `docker compose up -d --build php nginx` after pulling.
