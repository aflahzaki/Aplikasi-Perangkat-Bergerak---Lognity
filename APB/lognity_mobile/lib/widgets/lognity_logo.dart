import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';

class LognityLogo extends StatelessWidget {
  final double size;
  final double fontSize;

  const LognityLogo({
    super.key,
    this.size = 40,
    this.fontSize = 24,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      width: size,
      height: size,
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(size * 0.25),
        gradient: const LinearGradient(
          colors: [
            Color(0xFF38BDF8), // Light Blue
            Color(0xFF818CF8), // Indigo
            Color(0xFFC084FC), // Purple
          ],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        boxShadow: [
          BoxShadow(
            color: const Color(0xFF818CF8).withOpacity(0.3),
            blurRadius: 8,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Center(
        child: Text(
          'L',
          style: GoogleFonts.poppins(
            color: Colors.white,
            fontSize: fontSize,
            fontWeight: FontWeight.bold,
            height: 1.1, // Center alignment adjustment
          ),
        ),
      ),
    );
  }
}
