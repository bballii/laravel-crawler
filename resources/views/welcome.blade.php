<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="font-sans antialiased dark:bg-black dark:text-white/50 p-4">
    <div class="bg-gray-50 text-black/50 dark:bg-black dark:text-white/50 flex justify-center">
        <!-- Container -->
        <div class="flex space-x-8">
            <!-- Form -->
            <div>
                <h2 class="text-xl font-bold mb-4">Search Manually</h2>
                <div class="w-full max-w-sm">
                    <div class="mb-4">
                        <label for="site" class="block text-sm font-bold mb-2">Site</label>
                        <input type="text" id="site" class="w-full p-2 border border-gray-300 rounded">
                        <span class="text-sm">Site URL Eg: qwerty.xyz.</span>
                    </div>
                    <div class="mb-4">
                        <label for="keyword" class="block text-sm font-bold mb-2">Keyword</label>
                        <input type="text" id="keyword" class="w-full p-2 border border-gray-300 rounded">
                        <span class="text-sm">Add keywords separated with commas. Eg: cake, berry</span>
                    </div>
                    <button id="apiButton"
                        class="w-full bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-700 transition duration-300">
                        Search
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Matched Content -->
    <div class="mt-8">
        <h2 class="text-xl font-bold mb-4">Matched Content</h2>
        <div id="matchedContent" class="space-y-4"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        document.querySelectorAll('.copy-text').forEach(item => {
            item.addEventListener('click', function (event) {
                event.preventDefault();
                const textToCopy = this.getAttribute('data-text');
                navigator.clipboard.writeText(textToCopy).then(() => {
                    console.log('Copied to clipboard: ' + textToCopy);
                }).catch(err => {
                    console.error('Error copying text: ', err);
                });
            });
        });

        document.getElementById('apiButton').addEventListener('click', function () {
            const site = document.getElementById('site').value;
            let keywords = document.getElementById('keyword').value;
            let keywordsarr = keywords.split(',').map(keyword => keyword.trim());

            axios.post('/start-crawler', {
                site: site,
                keywords: keywords
            })
                .then(function (response) {
                    // Handle success
                    console.log(response.data);
                    displayMatchedContent(response.data.matched, keywordsarr);
                })
                .catch(function (error) {
                    // Handle error
                    console.error(error);
                });
        });

        function displayMatchedContent(matched, keywords) {
            const matchedContentContainer = document.getElementById('matchedContent');
            matchedContentContainer.innerHTML = ''; // Clear previous content

            matched.forEach(content => {
                const div = document.createElement('div');
                div.classList.add('bg-white', 'border', 'shadow-md', 'rounded', 'p-4', 'mb-4');
                div.innerHTML = formatTagAttributes(content.tag, content.attributes) + highlightKeywords(content.html, keywords);
                matchedContentContainer.appendChild(div);
            });
        }

        function formatTagAttributes(tag, attributes) {
            let attributesString = '';
            if (attributes && Object.keys(attributes).length > 0) {
                attributesString = '<ul>';
                for (const [key, value] of Object.entries(attributes)) {
                    attributesString += `<li>${key}: ${value}</li>`;
                }
                attributesString += '</ul>';
            }
            return `<h2><b>Tag:</b> ${tag}</h2><h3><b>Attributes:</b></h3>${attributesString}<hr>`;
        }

        function highlightKeywords(content, keywords) {
            keywords.forEach(keyword => {
                const regex = new RegExp(keyword, 'gi');
                content = content.replace(regex, `<span class="bg-yellow-300">${keyword}</span>`);
            });
            return content;
        }
    </script>
</body>

</html>