#!/bin/bash

SUPERVISOR_CONF="/Users/emilliacantella/kuliah/skripsi/adminbot/supervisor/supervisor.conf"

function status() {
    echo "🔹 Status semua process:"
    supervisorctl -c "$SUPERVISOR_CONF" status
}

function start_all() {
    echo "▶️  Menjalankan semua process..."
    supervisorctl -c "$SUPERVISOR_CONF" start all
}

function stop_all() {
    echo "⏹  Menghentikan semua process..."
    supervisorctl -c "$SUPERVISOR_CONF" stop all
}

function restart_all() {
    echo "🔄  Merestart semua process..."
    supervisorctl -c "$SUPERVISOR_CONF" restart all
}

function restart_worker() {
    echo "🔄  Merestart Laravel Worker..."
    supervisorctl -c "$SUPERVISOR_CONF" restart laravel-worker:*
}

function restart_reverb() {
    echo "🔄  Merestart Laravel Reverb..."
    supervisorctl -c "$SUPERVISOR_CONF" restart laravel-reverb
}

while true; do
    echo ""
    echo "=============================="
    echo "  Supervisord Manager AdminBot"
    echo "=============================="
    echo "1) Status semua process"
    echo "2) Start semua process"
    echo "3) Stop semua process"
    echo "4) Restart semua process"
    echo "5) Restart Laravel Worker saja"
    echo "6) Restart Laravel Reverb saja"
    echo "0) Keluar"
    echo -n "Pilih opsi: "
    read choice

    case "$choice" in
        1) status ;;
        2) start_all ;;
        3) stop_all ;;
        4) restart_all ;;
        5) restart_worker ;;
        6) restart_reverb ;;
        0) echo "👋 Keluar..."; exit 0 ;;
        *) echo "❌ Opsi tidak valid" ;;
    esac
done

