# Changelog

All notable changes to this project will be documented in this file.

## 1.1.0 - 2026-10-30

### Added

- Progress bar at the top of the page while an action, search, pagination or tab change is loading.

### Changed

- "Retry all" and "Delete all" run in a queued job (`ProcessFailedJobs`), so large lists no longer time out the request. Without a filter they now cover every stored failed job, not only `search_limit`.
- Deleting a single failed job now shows a toast.

### Fixed

- Clearing a queue uses the connection of the supervisor that works it in the current environment (`horizon.environments` merged with `horizon.defaults`), not always the first default.
- Combined workload rows (comma-joined queue names) are refused server-side when clearing.

## 1.0.1 - 2026-10-30

### Changed

- Destructive actions (pause/restart supervisors, clear queue, delete/retry failed jobs, stop monitoring) now ask for confirmation in a Flux modal instead of the browser's `confirm()` dialog.

## 1.0.0 - 2026-10-30

First release, for Livewire 3. Laravel Horizon's dashboard rewritten with Livewire and Flux Pro, plus search and queue filters on every job list, bulk retry/delete of failed jobs, pause/continue/restart of Horizon and its supervisors, and clearing a queue.

## [Unreleased]
