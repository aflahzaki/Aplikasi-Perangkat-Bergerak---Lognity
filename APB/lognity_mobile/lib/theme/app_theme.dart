import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';

class AppTheme {
  // ── Custom Error Dialog ──
  static void showCustomErrorDialog(BuildContext context, String title, String rawMessage) {
    String message = rawMessage;
    
    // Attempt to parse JSON error message from backend
    try {
      if (rawMessage.contains('{"message"')) {
        final startIndex = rawMessage.indexOf('{');
        final jsonString = rawMessage.substring(startIndex);
        final json = jsonDecode(jsonString);
        if (json is Map && json.containsKey('message')) {
          message = json['message'];
        }
      } else {
        final json = jsonDecode(rawMessage);
        if (json is Map && json.containsKey('message')) {
          message = json['message'];
        }
      }
    } catch (_) {}

    showDialog(
      context: context,
      builder: (ctx) {
        final isDark = Theme.of(ctx).brightness == Brightness.dark;
        return Dialog(
          backgroundColor: Colors.transparent,
          child: Container(
            padding: const EdgeInsets.all(24),
            decoration: glassDecoration(isDark, borderRadius: 24),
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                Container(
                  padding: const EdgeInsets.all(16),
                  decoration: BoxDecoration(
                    color: Colors.red.withOpacity(0.2),
                    shape: BoxShape.circle,
                  ),
                  child: const Icon(Icons.error_outline_rounded, color: Colors.redAccent, size: 48),
                ),
                const SizedBox(height: 20),
                Text(
                  title,
                  style: GoogleFonts.poppins(
                    fontSize: 20,
                    fontWeight: FontWeight.bold,
                    color: isDark ? Colors.white : Colors.grey.shade800,
                  ),
                ),
                const SizedBox(height: 12),
                Text(
                  message,
                  textAlign: TextAlign.center,
                  style: GoogleFonts.poppins(
                    fontSize: 14,
                    color: isDark ? Colors.grey.shade300 : Colors.grey.shade600,
                  ),
                ),
                const SizedBox(height: 24),
                SizedBox(
                  width: double.infinity,
                  child: ElevatedButton(
                    style: ElevatedButton.styleFrom(
                      backgroundColor: Colors.redAccent,
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                      padding: const EdgeInsets.symmetric(vertical: 14),
                    ),
                    onPressed: () => Navigator.pop(ctx),
                    child: Text('Mengerti', style: GoogleFonts.poppins(fontWeight: FontWeight.bold, color: Colors.white)),
                  ),
                ),
              ],
            ),
          ),
        );
      },
    );
  }

  // ── Brand Colors (matching Lognity website tailwind.config.js) ──
  static const Color lognity50  = Color(0xFFf0f9ff);
  static const Color lognity100 = Color(0xFFe0f2fe);
  static const Color lognity200 = Color(0xFFbae6fd);
  static const Color lognity300 = Color(0xFF7dd3fc);
  static const Color lognity400 = Color(0xFF38bdf8);
  static const Color lognity500 = Color(0xFF0ea5e9);
  static const Color lognity600 = Color(0xFF0284c7); // Primary
  static const Color lognity700 = Color(0xFF0369a1);
  static const Color lognity800 = Color(0xFF075985);
  static const Color lognity900 = Color(0xFF0c4a6e);

  // Fun accent colors
  static const Color funPink   = Color(0xFFf472b6);
  static const Color funPurple = Color(0xFFa78bfa);
  static const Color funYellow = Color(0xFFfde047);
  static const Color funLime   = Color(0xFFbef264);

  // Dark mode
  static const Color bgDark   = Color(0xFF0f172a);
  static const Color cardDark = Color(0xFF1e293b);
  static const Color textDark = Color(0xFFe2e8f0);

  // Gradient presets
  static const LinearGradient primaryGradient = LinearGradient(
    colors: [lognity600, funPurple],
    begin: Alignment.centerLeft,
    end: Alignment.centerRight,
  );

  static const LinearGradient heroGradient = LinearGradient(
    colors: [lognity600, Color(0xFF7c3aed)], // lognity → violet
    begin: Alignment.topLeft,
    end: Alignment.bottomRight,
  );

  static const LinearGradient pinkGradient = LinearGradient(
    colors: [funPink, Color(0xFFef4444)],
    begin: Alignment.topLeft,
    end: Alignment.bottomRight,
  );

  static const LinearGradient greenGradient = LinearGradient(
    colors: [Color(0xFF22c55e), Color(0xFF15803d)],
    begin: Alignment.topLeft,
    end: Alignment.bottomRight,
  );

  static const LinearGradient orangeGradient = LinearGradient(
    colors: [Color(0xFFf97316), Color(0xFFea580c)],
    begin: Alignment.centerLeft,
    end: Alignment.centerRight,
  );

  // ── Text Themes ──
  static TextTheme _buildTextTheme(TextTheme base) {
    return GoogleFonts.poppinsTextTheme(base);
  }

  // ── Glassmorphism Utility ──
  static BoxDecoration glassDecoration(bool isDark, {double borderRadius = 20}) {
    return BoxDecoration(
      color: isDark ? Colors.black.withOpacity(0.3) : Colors.white.withOpacity(0.6),
      borderRadius: BorderRadius.circular(borderRadius),
      border: Border.all(
        color: isDark ? Colors.white.withOpacity(0.05) : Colors.white.withOpacity(0.5),
        width: 1.5,
      ),
      boxShadow: [
        BoxShadow(
          color: isDark ? Colors.black.withOpacity(0.2) : Colors.black.withOpacity(0.05),
          blurRadius: 15,
          offset: const Offset(0, 8),
        ),
      ],
    );
  }

  // ── Input Decoration ──
  static InputDecorationTheme _inputDecoration(bool isDark) {
    return InputDecorationTheme(
      filled: true,
      fillColor: isDark ? cardDark : Colors.grey.shade50,
      contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
      border: OutlineInputBorder(
        borderRadius: BorderRadius.circular(12),
        borderSide: BorderSide(color: isDark ? Colors.grey.shade700 : Colors.grey.shade300),
      ),
      enabledBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(12),
        borderSide: BorderSide(color: isDark ? Colors.grey.shade700 : Colors.grey.shade300),
      ),
      focusedBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(12),
        borderSide: const BorderSide(color: lognity500, width: 2),
      ),
      hintStyle: TextStyle(color: isDark ? Colors.grey.shade500 : Colors.grey.shade400),
    );
  }

  // ── Light Theme ──
  static ThemeData lightTheme = ThemeData(
    brightness: Brightness.light,
    primaryColor: lognity600,
    scaffoldBackgroundColor: lognity50,
    textTheme: _buildTextTheme(ThemeData.light().textTheme),
    colorScheme: const ColorScheme.light(
      primary: lognity600,
      secondary: funPurple,
      surface: Colors.white,
      tertiary: funPink,
    ),
    appBarTheme: AppBarTheme(
      backgroundColor: Colors.transparent,
      foregroundColor: Colors.white,
      elevation: 0,
      centerTitle: false,
      titleTextStyle: GoogleFonts.poppins(
        fontWeight: FontWeight.bold,
        fontSize: 20,
        color: Colors.white,
      ),
      iconTheme: const IconThemeData(color: Colors.white),
    ),
    cardTheme: CardThemeData(
      color: Colors.white,
      elevation: 0,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(20),
        side: BorderSide(color: Colors.grey.shade200),
      ),
    ),
    elevatedButtonTheme: ElevatedButtonThemeData(
      style: ElevatedButton.styleFrom(
        backgroundColor: lognity600,
        foregroundColor: Colors.white,
        elevation: 0,
        padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 14),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
        textStyle: GoogleFonts.poppins(fontWeight: FontWeight.w600, fontSize: 15),
      ),
    ),
    floatingActionButtonTheme: const FloatingActionButtonThemeData(
      backgroundColor: lognity600,
      foregroundColor: Colors.white,
      elevation: 4,
      shape: CircleBorder(),
    ),
    inputDecorationTheme: _inputDecoration(false),
    bottomNavigationBarTheme: BottomNavigationBarThemeData(
      backgroundColor: Colors.white,
      selectedItemColor: lognity600,
      unselectedItemColor: Colors.grey[400],
      type: BottomNavigationBarType.fixed,
      selectedLabelStyle: GoogleFonts.poppins(fontWeight: FontWeight.w600, fontSize: 12),
      unselectedLabelStyle: GoogleFonts.poppins(fontSize: 12),
    ),
    dividerTheme: DividerThemeData(color: Colors.grey.shade200, thickness: 1),
    useMaterial3: true,
  );

  // ── Dark Theme ──
  static ThemeData darkTheme = ThemeData(
    brightness: Brightness.dark,
    primaryColor: lognity500,
    scaffoldBackgroundColor: bgDark,
    textTheme: _buildTextTheme(ThemeData.dark().textTheme),
    colorScheme: const ColorScheme.dark(
      primary: lognity500,
      secondary: funPurple,
      surface: cardDark,
      tertiary: funPink,
    ),
    appBarTheme: AppBarTheme(
      backgroundColor: Colors.transparent,
      foregroundColor: textDark,
      elevation: 0,
      centerTitle: false,
      titleTextStyle: GoogleFonts.poppins(
        fontWeight: FontWeight.bold,
        fontSize: 20,
        color: textDark,
      ),
      iconTheme: const IconThemeData(color: textDark),
    ),
    cardTheme: CardThemeData(
      color: cardDark,
      elevation: 0,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(20),
        side: BorderSide(color: Colors.grey.shade800),
      ),
    ),
    elevatedButtonTheme: ElevatedButtonThemeData(
      style: ElevatedButton.styleFrom(
        backgroundColor: lognity500,
        foregroundColor: Colors.white,
        elevation: 0,
        padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 14),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
        textStyle: GoogleFonts.poppins(fontWeight: FontWeight.w600, fontSize: 15),
      ),
    ),
    floatingActionButtonTheme: const FloatingActionButtonThemeData(
      backgroundColor: lognity500,
      foregroundColor: Colors.white,
      elevation: 4,
      shape: CircleBorder(),
    ),
    inputDecorationTheme: _inputDecoration(true),
    bottomNavigationBarTheme: BottomNavigationBarThemeData(
      backgroundColor: cardDark,
      selectedItemColor: lognity400,
      unselectedItemColor: Colors.grey[600],
      type: BottomNavigationBarType.fixed,
      selectedLabelStyle: GoogleFonts.poppins(fontWeight: FontWeight.w600, fontSize: 12),
      unselectedLabelStyle: GoogleFonts.poppins(fontSize: 12),
    ),
    dividerTheme: DividerThemeData(color: Colors.grey.shade800, thickness: 1),
    useMaterial3: true,
  );
}
