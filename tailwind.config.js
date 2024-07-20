import defaultTheme from "tailwindcss/defaultTheme";
import preset from "./vendor/filament/support/tailwind.config.preset";
import forms from "@tailwindcss/forms";
import flowbite from "flowbite/plugin";

/** @type {import('tailwindcss').Config} */
export default {
    presets: [preset],
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/**/*.js",
        "./node_modules/flowbite/**/*.js",
        "./app/Filament/**/*.php",
        "./vendor/filament/**/*.blade.php",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["Poppins", ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: "#20327C",
                secondary: "#A3BFFF",
                tertiary: "#1D0869",
            },
        },
    },

    plugins: [forms, flowbite],
};
