<?php

use function Livewire\Volt\layout;

layout('layouts.app');
?>

<div>
    <livewire:layout.header />
    <main>
        <div class="container mx-auto min-h-screen px-4 py-8">
            <div class="rounded-lg bg-white p-4 dark:bg-gray-800">
                <h1 class="mb-4 text-3xl font-bold text-gray-900 dark:text-white">How to Order</h1>
                <div class="mt-4">
                    <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3">
                        <div class="rounded-lg bg-white p-4 shadow-md dark:bg-gray-800">
                            <span class="text-2xl text-gray-900 dark:text-white">1</span>
                            <p>Login atau registrasi akun terlebih dahulu</p>
                        </div>
                        <div class="rounded-lg bg-white p-4 shadow-md dark:bg-gray-800">
                            <span class="text-2xl text-gray-900 dark:text-white">2</span>
                            <p>Pilih acara yang ingin Anda pesan tiketnya.</p>
                        </div>
                        <div class="rounded-lg bg-white p-4 shadow-md dark:bg-gray-800">
                            <span class="text-2xl text-gray-900 dark:text-white">3</span>
                            <p>Pilih paket tiket yang diinginkan.</p>
                        </div>
                        <div class="rounded-lg bg-white p-4 shadow-md dark:bg-gray-800">
                            <span class="text-2xl text-gray-900 dark:text-white">4</span>
                            <p>Klik tombol "Book Now" untuk melanjutkan proses pemesanan.</p>
                        </div>
                        <div class="rounded-lg bg-white p-4 shadow-md dark:bg-gray-800">
                            <span class="text-2xl text-gray-900 dark:text-white">5</span>
                            <p>Isi data diri Anda dengan lengkap.</p>
                        </div>
                        <div class="rounded-lg bg-white p-4 shadow-md dark:bg-gray-800">
                            <span class="text-2xl text-gray-900 dark:text-white">6</span>
                            <p>Pilih metode pembayaran yang diinginkan.</p>
                        </div>
                        <div class="rounded-lg bg-white p-4 shadow-md dark:bg-gray-800">
                            <span class="text-2xl text-gray-900 dark:text-white">7</span>
                            <p>Selesaikan pembayaran sesuai instruksi yang diberikan.</p>
                        </div>
                        <div class="rounded-lg bg-white p-4 shadow-md dark:bg-gray-800">
                            <span class="text-2xl text-gray-900 dark:text-white">8</span>
                            <p>Setelah pembayaran selesai, tiket dapat dilihat pada halaman My Tickets.</p>
                        </div>
                    </div>
                    </ol>
                </div>
            </div>
        </div>
    </main>
    <livewire:layout.footer />
</div>
