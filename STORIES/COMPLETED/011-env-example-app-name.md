# 011 - Document APP_NAME in .env.example

## Story

**As a** developer setting up KAAS locally or deploying it to a new environment,
**I want** the `.env.example` file to document the expected `APP_NAME` value with a comment,
**So that** I configure the application correctly without having to read the spec or the source.

## Background

The homepage `<title>` tag and nav bar read from `config('app.name')`, which is sourced from the `APP_NAME` environment variable. The correct value for this project is `Kobold As A Service`. The spec explicitly calls out that this is an environment concern, not a code change, and asks for it to be documented in `.env.example` with a comment.

## Acceptance Criteria

- [ ] `.env.example` contains `APP_NAME="Kobold As A Service"` (or `APP_NAME='Kobold As A Service'`).
- [ ] A comment line immediately above `APP_NAME` explains that this value is used as the page title and in the navigation bar, e.g.: `# Used as the page <title> and in the navigation bar`.
- [ ] The change is limited to `.env.example`; `.env` (the local secrets file) is not committed.
- [ ] No other environment variables are added or modified by this story.

## Out of Scope

- Any code change to controllers, views, or routes.
- Production deployment steps.

## Story Points: 1

## Priority: Low

## Dependencies

- Story 006 — the nav bar and `<title>` that consume `APP_NAME` should already exist for this change to be meaningful, but the `.env.example` update can be applied at any time.
