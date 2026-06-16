import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'dart:ui';
import '../services/api_service.dart';
import '../theme/app_theme.dart';
import 'leaderboard_screen.dart';

class DashboardScreen extends StatefulWidget {
  final Function(int)? onTabSelected;
  DashboardScreen({this.onTabSelected});

  @override
  _DashboardScreenState createState() => _DashboardScreenState();
}

class _DashboardScreenState extends State<DashboardScreen> {
  Map<String, dynamic>? stats;
  bool isLoading = true;

  @override
  void initState() {
    super.initState();
    _fetchStats();
  }

  void _fetchStats() async {
    try {
      final response = await ApiService.getDashboardStats();
      setState(() {
        stats = response;
        isLoading = false;
      });
    } catch (e) {
      setState(() { isLoading = false; });
    }
  }

  @override
  Widget build(BuildContext context) {
    if (isLoading) return const Center(child: CircularProgressIndicator());
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final userStats = stats?['user_stats'] ?? {};
    final globalStats = stats?['global_stats'] ?? {};

    return Stack(
      children: [
        // ── Animated Background Orbs ──
        Positioned(
          top: -100,
          right: -50,
          child: Container(
            width: 300,
            height: 300,
            decoration: BoxDecoration(
              shape: BoxShape.circle,
              color: AppTheme.funPurple.withOpacity(isDark ? 0.2 : 0.1),
            ),
          ),
        ),
        Positioned(
          bottom: 100,
          left: -100,
          child: Container(
            width: 250,
            height: 250,
            decoration: BoxDecoration(
              shape: BoxShape.circle,
              color: AppTheme.lognity500.withOpacity(isDark ? 0.15 : 0.1),
            ),
          ),
        ),
        // Blur Layer
        Positioned.fill(
          child: BackdropFilter(
            filter: ImageFilter.blur(sigmaX: 40, sigmaY: 40),
            child: Container(color: Colors.transparent),
          ),
        ),

        // ── Main Content ──
        SingleChildScrollView(
          child: Column(
            children: [
              // ── HERO BANNER ──
              Container(
                width: double.infinity,
                padding: const EdgeInsets.fromLTRB(24, 28, 24, 28),
                decoration: BoxDecoration(
                  gradient: AppTheme.heroGradient,
                  borderRadius: const BorderRadius.only(
                    bottomLeft: Radius.circular(32),
                    bottomRight: Radius.circular(32),
                  ),
                  boxShadow: [
                    BoxShadow(
                      color: AppTheme.funPurple.withOpacity(0.3),
                      blurRadius: 20,
                      offset: const Offset(0, 10),
                    ),
                  ],
                ),
                child: Stack(
                  children: [
                    Positioned(top: -30, right: -30, child: Container(width: 120, height: 120, decoration: BoxDecoration(color: Colors.white.withOpacity(0.08), shape: BoxShape.circle))),
                Positioned(bottom: -20, left: -20, child: Container(width: 80, height: 80, decoration: BoxDecoration(color: AppTheme.funYellow.withOpacity(0.15), shape: BoxShape.circle))),
                Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      children: [
                        CircleAvatar(
                          radius: 28,
                          backgroundColor: Colors.white.withOpacity(0.2),
                          backgroundImage: userStats['profil_url'] != null
                              ? NetworkImage(ApiService.parseUrl(userStats['profil_url']))
                              : null,
                          child: userStats['profil_url'] == null
                              ? const Icon(Icons.person_rounded, size: 28, color: Colors.white)
                              : null,
                        ),
                        const SizedBox(width: 16),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text('Halo, ${userStats['username'] ?? 'Guest'}! 👋',
                                style: GoogleFonts.poppins(fontSize: 22, fontWeight: FontWeight.bold, color: Colors.white)),
                              const SizedBox(height: 2),
                              Text('Siap untuk berkontribusi hari ini?',
                                style: GoogleFonts.poppins(fontSize: 13, color: Colors.white70)),
                            ],
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 20),
                    // Role & Level pills
                    Wrap(
                      spacing: 8,
                      children: [
                        _heroPill('👑 ${userStats['role'] ?? 'User'}'),
                        _heroPill('⭐ Level ${userStats['level'] ?? 'Maba'}'),
                      ],
                    ),
                  ],
                ),
              ],
            ),
          ),
          
          // ── CONTENT CARDS ──
          Padding(
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const SizedBox(height: 8),
                // ── ROW: XP + STATUS ──
                Row(
                  children: [
                    // XP Card
                    Expanded(child: _buildXpCard(userStats, isDark)),
                    const SizedBox(width: 12),
                    // Status Card
                    Expanded(child: _buildStatusCard(userStats, isDark)),
                  ],
                ),
                const SizedBox(height: 16),
                _buildQuotaTracker(userStats, isDark),
                const SizedBox(height: 16),

                // ── Forum Card (Pink Gradient) ──
                GestureDetector(
                  onTap: () {
                    widget.onTabSelected?.call(0);
                  },
                  child: Container(
                    width: double.infinity,
                    padding: const EdgeInsets.all(20),
                    decoration: BoxDecoration(
                      gradient: AppTheme.pinkGradient,
                      borderRadius: BorderRadius.circular(24),
                      boxShadow: [BoxShadow(color: AppTheme.funPink.withOpacity(0.3), blurRadius: 12, offset: const Offset(0, 4))],
                    ),
                    child: Row(
                      children: [
                        Container(
                          padding: const EdgeInsets.all(10),
                          decoration: BoxDecoration(color: Colors.white.withOpacity(0.2), borderRadius: BorderRadius.circular(14)),
                          child: const Icon(Icons.forum_rounded, color: Colors.white, size: 24),
                        ),
                        const SizedBox(width: 16),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text('Forum Diskusi', style: GoogleFonts.poppins(fontSize: 18, fontWeight: FontWeight.bold, color: Colors.white)),
                              const SizedBox(height: 2),
                              Text('${globalStats['total_forums'] ?? 0} diskusi aktif', style: GoogleFonts.poppins(fontSize: 12, color: Colors.white70)),
                            ],
                          ),
                        ),
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                          decoration: BoxDecoration(color: Colors.white.withOpacity(0.2), borderRadius: BorderRadius.circular(20)),
                          child: Row(
                            children: [
                              Text('Buka', style: GoogleFonts.poppins(fontSize: 12, fontWeight: FontWeight.bold, color: Colors.white)),
                              const SizedBox(width: 4),
                              const Icon(Icons.arrow_forward_rounded, size: 14, color: Colors.white),
                            ],
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
                const SizedBox(height: 16),

                // ── E-Library Card (Green Gradient) ──
                GestureDetector(
                  onTap: () {
                    widget.onTabSelected?.call(2);
                  },
                  child: Container(
                    width: double.infinity,
                    padding: const EdgeInsets.all(20),
                    decoration: BoxDecoration(
                      gradient: AppTheme.greenGradient,
                      borderRadius: BorderRadius.circular(24),
                      boxShadow: [BoxShadow(color: const Color(0xFF22c55e).withOpacity(0.25), blurRadius: 12, offset: const Offset(0, 4))],
                    ),
                    child: Row(
                      children: [
                        Container(
                          padding: const EdgeInsets.all(10),
                          decoration: BoxDecoration(color: Colors.white.withOpacity(0.2), borderRadius: BorderRadius.circular(14)),
                          child: const Icon(Icons.menu_book_rounded, color: Colors.white, size: 24),
                        ),
                        const SizedBox(width: 16),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text('E-Library', style: GoogleFonts.poppins(fontSize: 18, fontWeight: FontWeight.bold, color: Colors.white)),
                              const SizedBox(height: 2),
                              Text('Akses buku & referensi belajar', style: GoogleFonts.poppins(fontSize: 12, color: Colors.white70)),
                            ],
                          ),
                        ),
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                          decoration: BoxDecoration(color: Colors.white.withOpacity(0.2), borderRadius: BorderRadius.circular(20)),
                          child: Row(
                            children: [
                              Text('Jelajahi', style: GoogleFonts.poppins(fontSize: 12, fontWeight: FontWeight.bold, color: Colors.white)),
                              const SizedBox(width: 4),
                              const Icon(Icons.arrow_forward_rounded, size: 14, color: Colors.white),
                            ],
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
                const SizedBox(height: 16),

                // ── Leaderboard Banner ──
                GestureDetector(
                  onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => LeaderboardScreen())),
                  child: Container(
                    width: double.infinity,
                    padding: const EdgeInsets.all(20),
                    decoration: BoxDecoration(
                      gradient: AppTheme.orangeGradient,
                      borderRadius: BorderRadius.circular(24),
                      boxShadow: [BoxShadow(color: Colors.orange.withOpacity(0.3), blurRadius: 12, offset: const Offset(0, 4))],
                    ),
                    child: Row(
                      children: [
                        const Text('🏆', style: TextStyle(fontSize: 36)),
                        const SizedBox(width: 16),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text('Papan Peringkat', style: GoogleFonts.poppins(color: Colors.white, fontSize: 18, fontWeight: FontWeight.bold)),
                              const SizedBox(height: 2),
                              Text('Lihat Top Contributors!', style: GoogleFonts.poppins(color: Colors.white70, fontSize: 12)),
                            ],
                          ),
                        ),
                        const Icon(Icons.chevron_right_rounded, color: Colors.white, size: 28),
                      ],
                    ),
                  ),
                ),

                // ── Topik Sedang Hangat ──
                const SizedBox(height: 16),
                GestureDetector(
                  onTap: () {
                    widget.onTabSelected?.call(0);
                  },
                  child: Container(
                    width: double.infinity,
                    padding: const EdgeInsets.all(24),
                    decoration: AppTheme.glassDecoration(isDark, borderRadius: 24),
                    child: Column(
                      children: [
                        Container(
                          width: 56, height: 56,
                          decoration: BoxDecoration(
                            gradient: LinearGradient(colors: [Colors.orange.shade100, Colors.orange.shade200]),
                            borderRadius: BorderRadius.circular(20),
                          ),
                          child: const Center(child: Text('🔥', style: TextStyle(fontSize: 28))),
                        ),
                        const SizedBox(height: 12),
                        Text('Topik Sedang Hangat', style: GoogleFonts.poppins(fontWeight: FontWeight.bold, fontSize: 16, color: isDark ? Colors.white : Colors.grey.shade800)),
                        const SizedBox(height: 6),
                        Text('Banyak mahasiswa sedang berdiskusi. Jangan sampai ketinggalan!',
                          textAlign: TextAlign.center,
                          style: GoogleFonts.poppins(fontSize: 12, color: Colors.grey.shade500)),
                      ],
                    ),
                  ),
                ),
                const SizedBox(height: 24),
              ],
            ),
          ),
        ],
          ),
        ),
      ],
    );
  }

  Widget _heroPill(String text) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
      decoration: BoxDecoration(
        color: Colors.white.withOpacity(0.15),
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: Colors.white.withOpacity(0.15)),
      ),
      child: Text(text, style: GoogleFonts.poppins(fontSize: 12, fontWeight: FontWeight.w600, color: Colors.white)),
    );
  }

  Widget _buildXpCard(Map<String, dynamic> userStats, bool isDark) {
    final points = userStats['points'] ?? 0;
    final progress = (points / 8000).clamp(0.0, 1.0);

    return Container(
      padding: const EdgeInsets.all(18),
      decoration: AppTheme.glassDecoration(isDark, borderRadius: 20),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text('TOTAL XP', style: GoogleFonts.poppins(fontSize: 10, fontWeight: FontWeight.bold, color: Colors.grey.shade500, letterSpacing: 1)),
          const SizedBox(height: 8),
          Row(
            crossAxisAlignment: CrossAxisAlignment.baseline,
            textBaseline: TextBaseline.alphabetic,
            children: [
              Text('$points', style: GoogleFonts.poppins(fontSize: 28, fontWeight: FontWeight.w800, color: isDark ? Colors.white : Colors.grey.shade800)),
              const SizedBox(width: 4),
              Text('Pts', style: GoogleFonts.poppins(fontSize: 12, color: AppTheme.lognity500, fontWeight: FontWeight.w600)),
            ],
          ),
          const SizedBox(height: 12),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text('Progress', style: GoogleFonts.poppins(fontSize: 10, color: Colors.grey.shade400)),
              Text('${(progress * 100).toInt()}%', style: GoogleFonts.poppins(fontSize: 10, color: Colors.grey.shade400)),
            ],
          ),
          const SizedBox(height: 4),
          ClipRRect(
            borderRadius: BorderRadius.circular(6),
            child: LinearProgressIndicator(
              value: progress,
              minHeight: 6,
              backgroundColor: isDark ? Colors.grey.shade700 : Colors.grey.shade200,
              valueColor: const AlwaysStoppedAnimation<Color>(AppTheme.lognity500),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildStatusCard(Map<String, dynamic> userStats, bool isDark) {
    final points = userStats['points'] ?? 0;
    String statusText;
    Color statusColor;
    if (points < 500) {
      statusText = 'Starter';
      statusColor = Colors.red.shade400;
    } else if (points < 2500) {
      statusText = 'Member';
      statusColor = Colors.green;
    } else {
      statusText = 'Elite';
      statusColor = AppTheme.funPurple;
    }

    return Container(
      padding: const EdgeInsets.all(18),
      decoration: AppTheme.glassDecoration(isDark, borderRadius: 20),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text('STATUS AKUN', style: GoogleFonts.poppins(fontSize: 10, fontWeight: FontWeight.bold, color: Colors.grey.shade500, letterSpacing: 1)),
          const SizedBox(height: 8),
          Row(
            children: [
              Container(
                width: 40, height: 40,
                decoration: BoxDecoration(
                  color: AppTheme.funYellow.withOpacity(0.2),
                  borderRadius: BorderRadius.circular(12),
                ),
                child: const Center(child: Text('🛡', style: TextStyle(fontSize: 20))),
              ),
              const SizedBox(width: 10),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(userStats['role'] ?? 'User', style: GoogleFonts.poppins(fontSize: 16, fontWeight: FontWeight.bold, color: isDark ? Colors.white : Colors.grey.shade800)),
                    Text('Role Aktif', style: GoogleFonts.poppins(fontSize: 10, color: Colors.grey.shade400)),
                  ],
                ),
              ),
            ],
          ),
          const SizedBox(height: 12),
          Container(
            padding: const EdgeInsets.all(10),
            decoration: BoxDecoration(
              color: isDark ? Colors.grey.shade800 : Colors.grey.shade50,
              borderRadius: BorderRadius.circular(10),
              border: Border.all(color: isDark ? Colors.grey.shade700 : Colors.grey.shade200),
            ),
            child: Row(
              children: [
                Container(width: 6, height: 6, decoration: BoxDecoration(color: statusColor, shape: BoxShape.circle)),
                const SizedBox(width: 8),
                Expanded(child: Text(statusText, style: GoogleFonts.poppins(fontSize: 11, fontWeight: FontWeight.w600, color: statusColor))),
               ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildQuotaTracker(Map<String, dynamic> userStats, bool isDark) {
    final limits = userStats['limits'] ?? {};
    final reqUsed = limits['request_used'] ?? 0;
    final reqLimit = limits['request_limit'] ?? 0;
    final intUsed = limits['interaction_used'] ?? 0;
    final intLimit = limits['interaction_limit'] ?? 0;

    final reqProgress = reqLimit >= 9999 ? 0.0 : (reqUsed / reqLimit).clamp(0.0, 1.0);
    final intProgress = intLimit >= 9999 ? 0.0 : (intUsed / intLimit).clamp(0.0, 1.0);

    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(20),
      decoration: AppTheme.glassDecoration(isDark, borderRadius: 24),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                padding: const EdgeInsets.all(6),
                decoration: BoxDecoration(
                  color: AppTheme.lognity500.withOpacity(0.1),
                  borderRadius: BorderRadius.circular(8),
                ),
                child: const Icon(Icons.timer_outlined, size: 16, color: AppTheme.lognity600),
              ),
              const SizedBox(width: 8),
              Text(
                'KUOTA HARIAN',
                style: GoogleFonts.poppins(
                  fontSize: 11,
                  fontWeight: FontWeight.bold,
                  color: Colors.grey.shade500,
                  letterSpacing: 1.1,
                ),
              ),
            ],
          ),
          const SizedBox(height: 16),
          // Request Limit Row
          _buildQuotaRow(
            title: 'Request Forum',
            used: reqUsed,
            limit: reqLimit,
            progress: reqProgress,
            color: AppTheme.funYellow,
            isDark: isDark,
          ),
          const SizedBox(height: 16),
          // Interaction Limit Row
          _buildQuotaRow(
            title: 'Interaksi & Komentar',
            used: intUsed,
            limit: intLimit,
            progress: intProgress,
            color: AppTheme.funPink,
            isDark: isDark,
          ),
        ],
      ),
    );
  }

  Widget _buildQuotaRow({
    required String title,
    required int used,
    required int limit,
    required double progress,
    required Color color,
    required bool isDark,
  }) {
    final limitStr = limit >= 9999 ? '∞' : '$limit';
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Text(
              title,
              style: GoogleFonts.poppins(
                fontSize: 13,
                fontWeight: FontWeight.w600,
                color: isDark ? Colors.white70 : Colors.grey.shade700,
              ),
            ),
            Text(
              '$used / $limitStr',
              style: GoogleFonts.poppins(
                fontSize: 12,
                fontWeight: FontWeight.bold,
                color: color,
              ),
            ),
          ],
        ),
        const SizedBox(height: 6),
        ClipRRect(
          borderRadius: BorderRadius.circular(6),
          child: LinearProgressIndicator(
            value: limit >= 9999 ? 0.0 : progress,
            minHeight: 8,
            backgroundColor: isDark ? Colors.grey.shade700 : Colors.grey.shade100,
            valueColor: AlwaysStoppedAnimation<Color>(color),
          ),
        ),
      ],
    );
  }
}
