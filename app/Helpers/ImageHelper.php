<?php

if (!function_exists('uploadImage')) {

    function uploadImage($request, $fieldName, $folder)
    {
        if ($request->hasFile($fieldName)) {

            $image = $request->file($fieldName);
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

            $path = public_path("images/{$folder}");

            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }

            $image->move($path, $imageName);

            return "images/{$folder}/" . $imageName;
        }

        return null;
    }
}
