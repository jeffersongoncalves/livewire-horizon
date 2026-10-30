# Changelog

All notable changes to this project will be documented in this file.

## 2.0.1 - 2026-10-30

### Changed

- Destructive actions (pause/restart supervisors, clear queue, delete/retry failed jobs, stop monitoring) now ask for confirmation in a Flux modal instead of the browser's `confirm()` dialog.

## 2.0.0 - 2026-10-30

First release, for Livewire 4. Laravel Horizon's dashboard rewritten with Livewire and Flux Pro, plus search and queue filters on every job list, bulk retry/delete of failed jobs, pause/continue/restart of Horizon and its supervisors, and clearing a queue.

## [Unreleased]
