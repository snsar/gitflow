<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload File</title>
</head>

<body>
    <h1>Upload File</h1>
    <form id="uploadForm" enctype="multipart/form-data">
        <input type="file" name="file" id="file" required>
        <button type="submit">Upload</button>
    </form>
    <p id="result"></p>
    <h1>Get File</h1>
    <form id="getFileForm">
        <input type="text" name="filename" id="filename" placeholder="Enter filename" required>
        <button type="submit">Get File</button>
    </form>
    <p id="getFileResult"></p>
    <script>
        document.getElementById("uploadForm").addEventListener("submit", async function(event) {
            event.preventDefault();

            const formData = new FormData();
            const fileInput = document.getElementById("file");

            if (fileInput.files.length === 0) {
                document.getElementById("result").innerText = "Please select a file.";
                return;
            }

            formData.append("file", fileInput.files[0]);

            try {
                const response = await fetch("/upload", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}" // Laravel CSRF token
                    },
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    document.getElementById("result").innerText = "File uploaded successfully: " + result
                        .filename;
                } else {
                    document.getElementById("result").innerText = "Error: " + (result.error || "Upload failed");
                }
            } catch (error) {
                console.error(error);
                document.getElementById("result").innerText = "An error occurred while uploading.";
            }
        });


        document.getElementById("getFileForm").addEventListener("submit", async function(event) {
            event.preventDefault();

            const filename = document.getElementById("filename").value;
            if (!filename) {
                document.getElementById("getFileResult").innerText = "Please enter a filename.";
                return;
            }

            try {
                const response = await fetch(`/file/${filename}`, {
                    method: "GET",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute(
                            "content")
                    }
                });

                if (response.ok) {
                    const blob = await response.blob();
                    const url = URL.createObjectURL(blob);
                    const a = document.createElement("a");
                    a.href = url;
                    a.download = filename;
                    a.click();
                    URL.revokeObjectURL(url);
                    document.getElementById("getFileResult").innerText = "File downloaded: " + filename;
                } else {
                    const result = await response.json();
                    document.getElementById("getFileResult").innerText = "Error: " + (result.error ||
                        "File not found");
                }
            } catch (error) {
                console.error(error);
                document.getElementById("getFileResult").innerText =
                    "An error occurred while fetching the file.";
            }
        });
    </script>
</body>

</html>
