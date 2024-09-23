# get latest commit message.
commit_message=$(git log -1 --pretty=%B)

# Check if the commit is empty to avoid : git commit –allow-empty
if [ -z "$commit_message" ]; then
  echo "Error: Your commit is empty. Please provide a descriptive commit message."
  exit 1
fi

# Check if the commit message matches one of the pattern
if ! echo "$commit_message" |
grep -E '^(feat|enhancement|fix|docs|style|refactor|perf|test|chore|ci|build)\(([A-Za-z0-9_-]+)\) ?: ?.+$' > /dev/null && ! echo "$commit_message" |
grep -E '^Merge pull request #[0-9]+ from [A-Za-z0-9.#_\/-]+$' > /dev/null && ! echo "$commit_message" |
grep -E "^Merge branch '[A-Za-z0-9.#_\/-]+' of .*$" > /dev/null && ! echo "$commit_message" |
grep -E "^Merge (?:remote-tracking )?branch '[A-Za-z0-9.#_\/-]+' into [A-Za-z0-9.#_\/-]+$" > /dev/null && ! echo "$commit_message" |
grep -E '^Revert ".*"$' > /dev/null; then
  echo "Error: Your commit message does not match the format:"
  echo "  <type>(<scope>): <short summary>"
  echo "or"
  echo "  Merge pull request #<pull request number> from <branch>"
  echo "or"
  echo "  Merge branch <branch> into <branch>"
  echo
  echo "Example:"
  echo "  feat(auth): add user authentication"
  echo "or"
  echo "  Merge pull request #001 from PDC-HCSE/hotfix/important-fix"
  echo "or"
  echo "  Merge branch 'master' into hotfix/{id}-important-fix"
  echo
  exit 1
fi
