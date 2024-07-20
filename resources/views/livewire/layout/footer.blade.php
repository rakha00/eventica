<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<footer class="border-t-2 border-t-primary bg-white dark:border-t-secondary dark:bg-gray-800">
    <div class="mx-auto w-full max-w-screen-xl p-4 py-6 lg:py-8">
        <div class="md:flex md:justify-between">
            <div class="mb-6 md:mb-0 md:flex md:items-center md:justify-between">
                <a class="flex items-center" href="/">
                    <img class="me-3 h-24" src="{{ asset('img/logo.webp') }}" alt="Logo" />
                </a>
                <div class="mt-4 md:ms-3 md:mt-0">
                    <p class="text-sm text-gray-500 dark:text-gray-400">HIMTI Gunadarma</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Email : gunadarma.himti@gmail.com</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Line : @gye8387a | #BiruBiruSatu</p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-8 sm:grid-cols-3 sm:gap-6">
                <div>
                    <h2 class="mb-6 text-sm font-semibold uppercase text-gray-900 dark:text-white">Resources</h2>
                    <ul class="font-medium text-gray-500 dark:text-gray-400">
                        <li class="mb-4">
                            <a class="hover:underline" href="https://www.gunadarma.ac.id/">Gunadarma</a>
                        </li>
                        <li>
                            <a class="hover:underline" href="https://www.instagram.com/himtiofficialmerch?igshid=YmMyMTA2M2Y%3D">Official Merch</a>
                        </li>
                    </ul>
                </div>
                <div>
                    <h2 class="mb-6 text-sm font-semibold uppercase text-gray-900 dark:text-white">Follow us</h2>
                    <ul class="font-medium text-gray-500 dark:text-gray-400">
                        <li class="mb-4">
                            <a class="hover:underline" href="https://www.instagram.com/himtiug/">Instagram</a>
                        </li>
                        <li>
                            <a class="hover:underline" href="https://www.youtube.com/channel/UCHhdR7OcY8jC-LHfB00E7Kg">Youtube</a>
                        </li>
                    </ul>
                </div>
                <div>
                    <h2 class="mb-6 text-sm font-semibold uppercase text-gray-900 dark:text-white">Legal</h2>
                    <ul class="font-medium text-gray-500 dark:text-gray-400">
                        <li class="mb-4">
                            <a class="hover:underline" href="#">Privacy Policy</a>
                        </li>
                        <li>
                            <a class="hover:underline" href="#">Terms &amp; Conditions</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <hr class="my-6 border-gray-200 dark:border-gray-700 sm:mx-auto lg:my-8" />
        <div class="sm:flex sm:items-center sm:justify-between">
            <span class="text-sm text-gray-500 dark:text-gray-400 sm:text-center">© 2024 <a class="hover:underline" href="#">HIMTI™</a>. All Rights Reserved.
            </span>
            <div class="mt-4 flex sm:mt-0 sm:justify-center">
                <a class="text-gray-500 hover:text-gray-900 dark:hover:text-white" href="https://www.instagram.com/himtiug/">
                    <x-bi-instagram class="h-4 w-4" />
                    <span class="sr-only">Instagram page</span>
                </a>
                <a class="ms-5 text-gray-500 hover:text-gray-900 dark:hover:text-white" href="https://www.youtube.com/channel/UCHhdR7OcY8jC-LHfB00E7Kg">
                    <x-bi-youtube class="h-4 w-4" />
                    <span class="sr-only">Youtube page</span>
                </a>
            </div>
        </div>
    </div>
</footer>
