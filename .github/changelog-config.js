module.exports = {
    types: [
        {types: ["feat", "feature"], label: "🎉 New Features"},
        {types: ["fix", "bugfix"], label: "🐛 Bugfixes"},
        {types: ["improvements", "enhancement"], label: "🔨 Improvements"},
        {types: ["perf"], label: "🏎️ Performance Improvements"},
        {types: ["build", "ci"], label: "🏗️ Build System"},
        {types: ["refactor"], label: "🪚 Refactors"},
        {types: ["doc", "docs"], label: "📚 Documentation Changes"},
        {types: ["test", "tests"], label: "🔍 Tests"},
        {types: ["style"], label: "💅 Code Style Changes"},
        {types: ["chore"], label: "🧹 Chores"},
        {types: ["other"], label: "Other Changes"},
    ],

    excludeTypes: ["other"],

    includeCommitBody: false,

    renderTypeSection: function (label, commits, includeCommitBody) {
        let text = `\n## ${label}\n`;

        commits.forEach(commit => {
            const scope = commit.scope ? `**${commit.scope}:** ` : "";
            text += `- ${scope}${commit.subject}\n`;
            if (commit.body && includeCommitBody) {
                text += `${commit.body} by ${commit.author}\n`;
            }
        });

        return text;
    },

    renderNotes: function (notes) {
        let text = `\n## BREAKING CHANGES\n`;

        notes.forEach(note => {
            text += `- due to [${note.commit.sha.substr(0, 6)}](${note.commit.url}): ${note.commit.subject}\n\n`;
            text += `${note.text}\n\n`;
        });

        return text;
    },

    renderChangelog: function (release, changes) {
        const now = new Date();
        return `# ${release} - ${now.toISOString().substr(0, 10)}\n\n` + changes + "\n\n";
    },
};
