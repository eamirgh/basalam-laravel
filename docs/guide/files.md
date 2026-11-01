# File Uploads

Before creating or updating product images or other media on Basalam, you must upload the file to Basalam storage to obtain its reference ID.

Access the service via `Basalam::files()`.

---

## Uploading Product Photos

Use `uploadProductPhoto()` to upload an image and return its integer or string ID:

### From Local File Path

```php
use Eamirgh\Basalam\Facades\Basalam;

$photoId = Basalam::files()->uploadProductPhoto(
    file: storage_path('app/public/products/shirt.jpg'),
    filename: 'shirt.jpg'
);

echo "Photo ID: {$photoId}"; // e.g. 238300331
```

### From Uploaded File (Controller)

```php
use Illuminate\Http\Request;
use Eamirgh\Basalam\Facades\Basalam;

public function upload(Request $request)
{
    $request->validate([
        'image' => 'required|image|max:5120',
    ]);

    $file = $request->file('image');

    $photoId = Basalam::files()->uploadProductPhoto(
        file: $file->getRealPath(),
        filename: $file->getClientOriginalName()
    );

    return response()->json(['photo_id' => $photoId]);
}
```

### From In-Memory Binary Stream

```php
$binaryData = file_get_contents('https://cdn.example.com/remote-image.jpg');

$photoId = Basalam::files()->uploadProductPhoto(
    file: $binaryData,
    filename: 'downloaded.jpg'
);
```

---

## Generic File Upload

For non-product files or custom file types:

```php
$response = Basalam::files()->upload(
    file: storage_path('app/documents/terms.pdf'),
    fileType: 'vendor.document',
    filename: 'terms.pdf'
);

echo $response['id'];
echo $response['path'];
```
