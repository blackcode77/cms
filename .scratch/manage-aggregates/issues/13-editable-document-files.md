Status: resolved

# 13: Editable Documents (file uploads) on /schedules and /dangerous-goods

Terms per `GLOSSARY.md` (**Document**). Decisions Q27–Q34.

**What to build:**
- Backend `POST /api/v1/uploads/file` (`edit_page`): PDF, XLSX, XLS, DOCX, DOC detected by content (PDF magic, OOXML zip with the matching `[Content_Types].xml` part, OLE2 for XLS/DOC); anything else (zip, exe, html, svg, …) rejected. Max 20 MB (raise nginx `client_max_body_size` and PHP limits; images stay 5 MB). Stored as `/uploads/files/<uuid>/<name>.<ext>`, name transliterated and reduced to `[a-z0-9-]`. Returns `{url, size}`.
- New Content value type `file` (path under `/uploads/files/`, empty = no file), validated by the backend like `image`.
- nginx (dev + prod): `/uploads/files/` sent with `Content-Disposition: attachment` and `nosniff`.
- Frontend: `file` field input (upload, shows name); the panel fills the Item's `size` field from the upload (bytes → «210 КБ»), the editor does not edit it; format label from the file extension at render.
- `/schedules`: the 4 categories and filters stay in code; each category's documents are a Content list (`docs.<category>.items`: title, note, file, size) with editable heading (`docs.<category>.title`). Current titles/notes are defaults without files.
- `/dangerous-goods`: aside documents become their own list (`aside.docs`), default «Памятка по ОГ» without a file.
- A Document without a file renders without the «Скачать» button.
- Replaced/deleted files stay on the server (no cleanup, by decision).

**Blocked by:** None

- [x] Upload accepts the five formats, rejects others, oversized and unauthenticated/unauthorized requests (Cest + unit for detection and name cleaning)
- [x] `file` value validation (only `/uploads/files/...`), Cest
- [x] Downloaded file has the cleaned original name; nginx forces download
- [x] Panel upload fills size; page shows format and size; no button without a file
- [x] /schedules and /dangerous-goods render as before with empty `page_content`; tests; lint

## Notes

- Format detection is structural, without ext-zip: `%PDF-`; ZIP with `[Content_Types].xml` + `xl/workbook`/`word/document`; OLE2 with a `Workbook`/`Book`/`WordDocument` stream name (UTF-16LE).
- Names: ICU `Russian-Latin/BGN; Any-Latin; Latin-ASCII` → `raspisaniye-noyabr.pdf`; fallback `document`.
- PHP image rebuilt for the new limits (`docker/php/uploads.ini` is baked into the image): `docker compose build php`.
- Panel: `file` field with `sizeField` fills a `hidden` sibling field with the formatted size on upload.
- Verified end to end: 15 MB PDF and XLSX uploaded through nginx, download has `Content-Disposition: attachment` + `nosniff`; saved list shows format, size and «Скачать» on /schedules.
