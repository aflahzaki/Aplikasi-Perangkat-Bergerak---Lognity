import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'dart:ui';
import '../services/api_service.dart';
import '../theme/app_theme.dart';
import 'user_profile_screen.dart';

class LeaderboardScreen extends StatefulWidget {
  @override
  _LeaderboardScreenState createState() => _LeaderboardScreenState();
}

class _LeaderboardScreenState extends State<LeaderboardScreen> {
  List<dynamic> users = [];
  bool isLoading = true;

  @override
  void initState() {
    super.initState();
    _fetchLeaderboard();
  }

  Future<void> _fetchLeaderboard() async {
    try {
      final data = await ApiService.getLeaderboard();
      setState(() => users = data);
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Gagal memuat papan peringkat')));
    } finally {
      setState(() => isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;

    return Scaffold(
      appBar: PreferredSize(
        preferredSize: const Size.fromHeight(56),
        child: Container(
          decoration: BoxDecoration(gradient: isDark ? null : AppTheme.orangeGradient, color: isDark ? AppTheme.bgDark : null),
          child: AppBar(
            backgroundColor: Colors.transparent, elevation: 0,
            title: Text('🏆 Top Contributors', style: GoogleFonts.poppins(fontWeight: FontWeight.bold, fontSize: 18)),
          ),
        ),
      ),
      body: Stack(
        children: [
          // ── Animated Background Orbs ──
          Positioned(
            top: -50,
            left: -50,
            child: Container(
              width: 300,
              height: 300,
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                color: AppTheme.lognity500.withOpacity(isDark ? 0.15 : 0.1),
              ),
            ),
          ),
          Positioned(
            bottom: 100,
            right: -100,
            child: Container(
              width: 250,
              height: 250,
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                color: AppTheme.funPink.withOpacity(isDark ? 0.2 : 0.1),
              ),
            ),
          ),
          Positioned.fill(
            child: BackdropFilter(
              filter: ImageFilter.blur(sigmaX: 40, sigmaY: 40),
              child: Container(color: Colors.transparent),
            ),
          ),
          isLoading
              ? const Center(child: CircularProgressIndicator())
              : ListView.builder(
                  padding: const EdgeInsets.fromLTRB(16, 8, 16, 16),
                  itemCount: users.length,
                  itemBuilder: (context, index) {
                    final user = users[index];
                    final bool isTop3 = index < 3;

                    if (isTop3) return _buildTopCard(user, index, isDark);
                    return _buildRegularCard(user, index, isDark);
                  },
                ),
        ],
      ),
    );
  }

  Widget _buildTopCard(Map<String, dynamic> user, int index, bool isDark) {
    final colors = [
      [const Color(0xFFFEF3C7), const Color(0xFFF59E0B), const Color(0xFFFBBF24)], // Gold
      [Colors.grey.shade100, Colors.grey.shade500, Colors.grey.shade400],            // Silver
      [const Color(0xFFFED7AA), const Color(0xFFEA580C), const Color(0xFFF97316)],   // Bronze
    ];
    final medals = ['🥇', '🥈', '🥉'];
    final bg = isDark ? AppTheme.cardDark : colors[index][0];
    final accent = colors[index][1] as Color;

    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      padding: const EdgeInsets.all(16),
      decoration: AppTheme.glassDecoration(isDark, borderRadius: 20).copyWith(
        color: isDark ? AppTheme.cardDark.withOpacity(0.6) : bg.withOpacity(0.6),
        border: Border.all(color: accent.withOpacity(isDark ? 0.3 : 0.4), width: 1.5),
        boxShadow: isDark ? [] : [BoxShadow(color: accent.withOpacity(0.15), blurRadius: 12, offset: const Offset(0, 4))],
      ),
      child: InkWell(
        onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => UserProfileScreen(userId: user['user_id']))),
        child: Row(
          children: [
            Text(medals[index], style: const TextStyle(fontSize: 28)),
            const SizedBox(width: 12),
            CircleAvatar(
              radius: 24,
              backgroundColor: accent.withOpacity(0.2),
              backgroundImage: user['profil_url'] != null ? NetworkImage(ApiService.parseUrl(user['profil_url'])) : null,
              child: user['profil_url'] == null ? Icon(Icons.person_rounded, color: accent) : null,
            ),
            const SizedBox(width: 14),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(user['username'] ?? 'User', style: GoogleFonts.poppins(fontWeight: FontWeight.bold, fontSize: 16, color: isDark ? Colors.white : Colors.grey.shade800)),
                  Text(user['current_level'] ?? 'Maba', style: GoogleFonts.poppins(fontSize: 12, color: accent, fontWeight: FontWeight.w500)),
                ],
              ),
            ),
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
              decoration: BoxDecoration(
                color: accent.withOpacity(isDark ? 0.2 : 0.15),
                borderRadius: BorderRadius.circular(20),
              ),
              child: Text('${user['points']} Pts', style: GoogleFonts.poppins(fontWeight: FontWeight.bold, fontSize: 13, color: accent)),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildRegularCard(Map<String, dynamic> user, int index, bool isDark) {
    return Container(
      margin: const EdgeInsets.only(bottom: 8),
      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
      decoration: AppTheme.glassDecoration(isDark, borderRadius: 14),
      child: InkWell(
        onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => UserProfileScreen(userId: user['user_id']))),
        child: Row(
          children: [
            SizedBox(
              width: 28,
              child: Text('#${index + 1}', style: GoogleFonts.poppins(fontSize: 14, fontWeight: FontWeight.bold, color: Colors.grey.shade500)),
            ),
            const SizedBox(width: 8),
            CircleAvatar(
              radius: 18,
              backgroundColor: isDark ? Colors.grey.shade700 : AppTheme.lognity100,
              backgroundImage: user['profil_url'] != null ? NetworkImage(ApiService.parseUrl(user['profil_url'])) : null,
              child: user['profil_url'] == null ? Icon(Icons.person_rounded, size: 18, color: Colors.grey.shade500) : null,
            ),
            const SizedBox(width: 12),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(user['username'] ?? 'User', style: GoogleFonts.poppins(fontWeight: FontWeight.w600, fontSize: 14)),
                  Text(user['current_level'] ?? 'Maba', style: GoogleFonts.poppins(fontSize: 11, color: AppTheme.lognity500)),
                ],
              ),
            ),
            Text('${user['points']} Pts', style: GoogleFonts.poppins(fontWeight: FontWeight.w600, fontSize: 13, color: Colors.orange.shade700)),
          ],
        ),
      ),
    );
  }
}
