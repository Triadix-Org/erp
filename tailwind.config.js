import preset from "./vendor/filament/support/tailwind.config.preset";

export default {
    presets: [preset],
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
        "./node_modules/flowbite/**/*.js",
        "app/Filament/**/*.php",
        "resources/views/filament/**/*.blade.php",
        "vendor/filament/**/*.blade.php",
        "vendor/awcodes/filament-table-repeater/resources/**/*.blade.php",
    ],
    theme: {
        fontFamily: {
            sans: ["ui-sans-serif", "system-ui"],
            serif: ["ui-serif", "Georgia"],
            mono: ["ui-monospace", "SFMono-Regular"],
            display: ["Oswald"],
            body: ['"Open Sans"'],
        },
    },
    plugins: [require("flowbite/plugin")],
};
