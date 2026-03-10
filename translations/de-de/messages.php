<?php
// messages.php
// German - DE

return [
    // Application general
    'application.general.title' => 'Mensch, ärgere dich nicht',
    'application.general.yes' => 'Ja', 
    'application.general.no' => 'Nein', 

    // Application - Menu
    'application.menu.title' => 'Hauptmenü', 
    'application.menu.lobby' => '🎮 Spiel Lobby', 
    'application.menu.account' => '👤 Benutzer Account', 
    'application.menu.admin' => '🛡 Admin', 

    // Game general
    'game.create.title' => 'Neues Spiel erstellen',
    'game.create.submit' => 'Spiel erstellen',
    'game.status.waiting' => 'Wartend',
    'game.status.running' => 'Laufend',
    'game.status.finished' => 'Beendet',
    
    // Games - Lobby
    'game.lobby.title' => '🎮 Spiel Lobby', 
    'game.lobby.create_new_game' => '➕ Neues Spiel erstellen', 
    'game.lobby.open_games' => '📜 Zeige offene Spiele', 
    'game.lobby.back_to_menu' => '← Zurück zum Hauptmenü', 

    // Games - List
    'game.list.title' => 'Offene Spiele',
    'game.list.empty' => 'Keine offenen Spiele vorhanden.',
    'game.list.id' => 'Spiel-ID',
    'game.list.creator' => 'Ersteller',
    'game.list.players' => 'Spieler',
    'game.list.status' => 'Status',
    'game.list.game_type' => 'Spieltyp',
    'game.list.type' => 'Typ',
    'game.list.created_by_username' => 'Erstellt von',
    'game.list.player_count' => 'Anzahl der Players',
    'game.list.options' => 'Optionen',
    'game.list.name' => 'Spielname', 
    'game.list.action' => 'Aktion',
    'game.list.join' => '➕ Beitreten',
    'game.list.join_icon' => '➕',
    'game.list.edit' => '✏️ Bearbeiten',
    'game.list.edit_icon' => '✏️',  
    'game.list.test_solo' => '🧪 Einzelspiel zum testen', 
    'game.list.test_solo_icon' => '🧪', 
    'game.list.delete' => '🗑 Löschen', 
    'game.list.delete_icon' => '🗑', 
    'game.list.delete_confirm' => 'Spiel wirklich löschen?', 
    'game.list.title' => 'Spieleliste', 
    'game.list.create_new_game' => '➕ Neues Spiel erstellen', 
    'game.list.back_to_lobby' => '← Zurück zur Lobby', 

    // Games - Create
    'game.create.button_create' => 'Spiel erstellen', 
    'game.create.three' => '3', 
    'game.create.name' => 'Name', 
    'game.create.game_name' => 'Spielname', 
    'game.create.game_options' => 'Spieloptionen', 
    'game.create.game_options' => 'Spielregeln', 

    //Games - Show / Detail
    'game.show.title' => 'Spiel', 
    'game.show.back_to_list' => '← Zurück zur Übersicht', 
    'game.show.solo_test_creation_confirm' => 'Möchtest du sicher eine Solosession aus diesem Spiel erstellen und starten?', 
    'game.show.status' => 'Status', 
    'game.show.join' => 'Beitreten',
    'game.show.leave' => 'Verlassen',
    'game.show.created_at' => '📅 Erstellt am', 
    'game.show.created_by' => '👤 Erstellt von', 
    'game.show.player' => 'Spieler', 
    'game.show.players' => '👥 Spieler', 
    'game.show.figure' => 'Figur', 
    'game.show.figures' => 'Figuren', 
    'game.show.position' => 'Position', 
    'game.show.area' => 'Bereich', 
    'game.show.rules' => '⚙ Regeln', 
    'game.show.test_solo_create' => 'Einzelspiel zum Testen erstellen', 
    'game.show.test_solo_start' => 'Einzelspiel zum Testen starten', 
    'game.show.test_solo_play' => 'Einzelspiel zum Testen spielen', 
    'game.show.test_solo_pause' => 'Einzelspiel zum Testen pausieren', 
    'game.show.test_solo_cancel' => 'Einzelspiel zum Testen abbrechen',
    'game.show.label_no_players_found' => 'Keine Spieler vorhanden.',
    'game.show.label_no_figures_found' => 'Keine Figuren vorhanden.',  
    'game.show.label_rules_bots_allows' => '🤖 Bots erlaubt', 
    'game.show.label_rules_leave_home_attempt' => '🏠 Mehrmaliges Würfeln bis zum Verlassen der Homzone von', 
    'game.show.label_rules_leave_home_attempt_enum_first_figure' => 'der ersten Figur', 
    'game.show.label_rules_leave_home_attempt_enum_all_figures' => 'allen Figuren', 
    'game.show.label_rules_leave_home_attempts_max' => '🔂 Wie viele Versuche zum Verlassen der Homezone bevor der nächste Spieler am Zug ist', 
    'game.show.label_rules_roll_on_six_limit' => '🎲 Extra-Wurf bei 6 Limit', 
    'game.show.label_rules_roll_on_six_limit_no' => 'Kein nochmaliges Würfeln nach 6.', 
    'game.show.label_rules_roll_on_six_limit_unlimited' => 'Nach jeder 6 ein weiteres Würfeln.', 
    'game.show.label_rules_roll_on_six_limit_limited' => 'Zusätzliches Würfeln nach einer 6 ist begrenzt auf:', 
    'game.show.label_rules_force_extra_lap_on_overflow' => '➿ Weitere Runde bei Überwurf', 
    'game.show.label_rules_stack_own_figures' => '🧱 Eigene Figuren stapeln', 
    'game.show.label_rules_strict_goal_order' => '🎯 Strenge Zielfeld-Reihenfolge', 
    'game.show.label_rules_start_field_must_be_cleared' => '🚪 Startfeld muss frei sein', 

    // Game - Play a game
    'game.play.status' => 'Status', 
    'game.play.current_player' => 'Aktueller Spieler', 
    'game.play.roll_dice' => 'Würfelwert', 
    'game.play.possible_moves' => 'Mögliche Bewegungen', 
    'game.play.figure' => 'Figur', 
    'game.play.figures' => 'Figuren', 
    'game.play.move_figure' => 'Bewege Figur ', 
    'game.play.position' => 'Position', 

    // Game - Options
    'game.options.is_private' => '⛔ Privates Spiel',
    'game.options.is_locked' => '🔒 Geschlossenes Spiel',

    // Game - Rules
    'game.rules.allow_bots' => '🤖 Bots erlauben',
    'game.rules.leave_home_attempt' => '🏠 Mehrmaliges Würfeln bis zum Verlassen der Homzone von', 
    'game.rules.leave_home_attempt_enum_first_figure' => 'der ersten Figur', 
    'game.rules.leave_home_attempt_enum_all_figures' => 'allen Figuren', 
    'game.rules.leave_home_attempts_max' => '🔂 Wie viele Versuche zum Verlassen der Homezone bevor der nächste Spieler am Zug ist',
    'game.rules.roll_on_six_limit' => '🎲 Extra Wurf bei einer 6',
    'game.rules.force_extra_lap_on_overflow' => '➿ Weitere Runde bei Überwurf', 
    'game.rules.allow_stack_own_figures' => '🧱 Eigene Figuren stapeln erlauben',
    'game.rules.strict_goal_order' => '🎯 Strikte Zielreihenfolge',
    'game.rules.start_field_must_be_cleared' => '🚪 Startfeld muss frei sein',

    // Admin 
    'admin.dashboard.title' => 'Admin Übersicht', 
    'admin.users.manage' => 'Benutzer bearbeiten', 
    'admin.games.manage' => 'Spiele bearbeten', 
];
