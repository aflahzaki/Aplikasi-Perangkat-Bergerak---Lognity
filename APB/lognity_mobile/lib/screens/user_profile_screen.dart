import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'dart:ui';
import '../services/api_service.dart';
import '../theme/app_theme.dart';
import 'forum/forum_detail_screen.dart';
import 'social/chat_detail_screen.dart';

class UserProfileScreen extends StatefulWidget {
  final int userId;

  UserProfileScreen({required this.userId});

  @override
  _UserProfileScreenState createState() => _UserProfileScreenState();
}

class _UserProfileScreenState extends State<UserProfileScreen> {
  Map<String, dynamic>? userProfile;
  List<dynamic> recentRequests = [];
  List<dynamic> recentInteractions = [];
  bool isLoading = true;
  bool isFollowing = false;
  bool isFollowLoading = false;

  @override
  void initState() {
    super.initState();
    _fetchProfile();
  }

  void _fetchProfile() async {
    try {
      final response = await ApiService.getUserProfile(widget.userId);
      final followingStatus = await ApiService.getFollowStatus(widget.userId);
      setState(() {
        userProfile = response['user'];
        recentRequests = response['recent_requests'] ?? [];
        recentInteractions = response['recent_interactions'] ?? [];
        isFollowing = followingStatus;
        isLoading = false;
      });
    } catch (e) {
      setState(() { isLoading = false; });
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: const Text('Gagal memuat profil pengguna'),
          backgroundColor: Colors.red.shade400,
          behavior: SnackBarBehavior.floating,
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
        ),
      );
    }
  }

  void _showReportDialog() {
    final reportController = TextEditingController();
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(24)),
        title: Text('Laporkan Pengguna', style: GoogleFonts.poppins(fontWeight: FontWeight.bold)),
        content: TextField(
          controller: reportController,
          maxLines: 3,
          decoration: InputDecoration(
            hintText: 'Contoh: Perilaku kasar, Spam, Akun palsu...',
            hintStyle: GoogleFonts.poppins(fontSize: 13),
            border: OutlineInputBorder(borderRadius: BorderRadius.circular(14)),
          ),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: Text('Batal', style: GoogleFonts.poppins()),
          ),
          ElevatedButton(
            style: ElevatedButton.styleFrom(
              backgroundColor: Colors.redAccent,
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
            ),
            onPressed: () async {
              if (reportController.text.trim().isEmpty) return;
              Navigator.pop(context);
              try {
                final res = await ApiService.reportContent('user', widget.userId, reportController.text.trim());
                ScaffoldMessenger.of(context).showSnackBar(
                  SnackBar(
                    content: Text(res['message'] ?? 'Laporan berhasil dikirim.'),
                    behavior: SnackBarBehavior.floating,
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                  ),
                );
              } catch (e) {
                ScaffoldMessenger.of(context).showSnackBar(
                  SnackBar(
                    content: const Text('Gagal melaporkan pengguna.'),
                    backgroundColor: Colors.red.shade400,
                    behavior: SnackBarBehavior.floating,
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                  ),
                );
              }
            },
            child: Text('Laporkan', style: GoogleFonts.poppins(color: Colors.white, fontWeight: FontWeight.bold)),
          ),
        ],
      ),
    );
  }

  Future<void> _toggleFollow() async {
    setState(() => isFollowLoading = true);
    try {
      if (isFollowing) {
        await ApiService.unfollowUser(widget.userId);
      } else {
        await ApiService.followUser(widget.userId);
      }
      setState(() {
        isFollowing = !isFollowing;
        isFollowLoading = false;
      });
    } catch (e) {
      setState(() => isFollowLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;

    if (isLoading) return Scaffold(body: const Center(child: CircularProgressIndicator()));
    if (userProfile == null) {
      return Scaffold(
        appBar: AppBar(title: const Text('Profil')),
        body: Center(child: Text('Pengguna tidak ditemukan', style: GoogleFonts.poppins())),
      );
    }

    final String username = userProfile?['username'] ?? 'Unknown';
    final String level = userProfile?['current_level'] ?? 'Mahasiswa';
    final int points = userProfile?['points'] ?? 0;
    final String role = userProfile?['role'] ?? 'User';
    final String? profilUrl = userProfile?['profil_url'];
    final List<dynamic> badges = userProfile?['badges'] ?? [];

    return Scaffold(
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
          // ── Main Content ──
          SingleChildScrollView(
            child: Column(
              children: [
            // ── Gradient Header ──
            Stack(
              clipBehavior: Clip.none,
              children: [
                Container(
                  height: 180,
                  width: double.infinity,
                  decoration: BoxDecoration(
                    gradient: AppTheme.heroGradient,
                    borderRadius: const BorderRadius.only(
                      bottomLeft: Radius.circular(32),
                      bottomRight: Radius.circular(32),
                    ),
                  ),
                  child: Stack(
                    children: [
                      Positioned(top: -30, right: -30, child: Container(width: 120, height: 120, decoration: BoxDecoration(color: Colors.white.withOpacity(0.08), shape: BoxShape.circle))),
                      Positioned(bottom: -20, left: -20, child: Container(width: 80, height: 80, decoration: BoxDecoration(color: AppTheme.funYellow.withOpacity(0.15), shape: BoxShape.circle))),
                      SafeArea(
                        child: Padding(
                          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                          child: Row(
                            children: [
                              IconButton(
                                icon: const Icon(Icons.arrow_back_rounded, color: Colors.white),
                                onPressed: () => Navigator.pop(context),
                              ),
                              Text(
                                'Profil Pengguna',
                                style: GoogleFonts.poppins(fontWeight: FontWeight.w600, fontSize: 18, color: Colors.white),
                              ),
                              const Spacer(),
                              IconButton(
                                icon: const Icon(Icons.flag_rounded, color: Colors.white),
                                onPressed: _showReportDialog,
                              ),
                            ],
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
                Positioned(
                  bottom: -40, left: 0, right: 0,
                  child: Center(
                    child: Container(
                      decoration: BoxDecoration(
                        shape: BoxShape.circle,
                        border: Border.all(color: isDark ? AppTheme.cardDark : Colors.white, width: 4),
                        boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.1), blurRadius: 12)],
                      ),
                      child: CircleAvatar(
                        radius: 48,
                        backgroundColor: isDark ? AppTheme.cardDark : AppTheme.lognity100,
                        backgroundImage: profilUrl != null ? NetworkImage(ApiService.parseUrl(profilUrl)) : null,
                        child: profilUrl == null ? const Icon(Icons.person_rounded, size: 48, color: AppTheme.lognity500) : null,
                      ),
                    ),
                  ),
                ),
              ],
            ),
            const SizedBox(height: 52),

            // ── User Info ──
            Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Text(
                  username,
                  style: GoogleFonts.poppins(fontSize: 22, fontWeight: FontWeight.bold, color: isDark ? Colors.white : Colors.grey.shade800),
                ),
                if (role.toLowerCase() == 'admin' || role.toLowerCase() == 'superadmin') ...[
                  const SizedBox(width: 8),
                  const Icon(Icons.verified_rounded, color: Colors.blue, size: 22),
                ]
              ],
            ),
            const SizedBox(height: 2),
            Text(
              '👑 $role',
              style: GoogleFonts.poppins(fontSize: 13, color: Colors.grey.shade500, fontWeight: FontWeight.w500),
            ),
            const SizedBox(height: 16),

            // ── Social Action Buttons ──
            Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                ElevatedButton(
                  onPressed: isFollowLoading ? null : _toggleFollow,
                  style: ElevatedButton.styleFrom(
                    backgroundColor: isFollowing ? Colors.grey.shade300 : AppTheme.lognity500,
                    foregroundColor: isFollowing ? Colors.black87 : Colors.white,
                    elevation: 0,
                    padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 12),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                  ),
                  child: isFollowLoading
                      ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(strokeWidth: 2))
                      : Text(
                          isFollowing ? 'Mengikuti' : 'Ikuti',
                          style: GoogleFonts.poppins(fontWeight: FontWeight.bold),
                        ),
                ),
                const SizedBox(width: 12),
                OutlinedButton.icon(
                  onPressed: () {
                    Navigator.push(
                      context,
                      MaterialPageRoute(
                        builder: (_) => ChatDetailScreen(
                          userId: widget.userId,
                          username: username,
                          profilUrl: profilUrl,
                        ),
                      ),
                    );
                  },
                  icon: const Icon(Icons.mail_outline_rounded, size: 18),
                  label: Text('Pesan', style: GoogleFonts.poppins(fontWeight: FontWeight.bold)),
                  style: OutlinedButton.styleFrom(
                    foregroundColor: isDark ? Colors.white : Colors.black87,
                    side: BorderSide(color: isDark ? Colors.grey.shade700 : Colors.grey.shade300),
                    padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 12),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                  ),
                ),
              ],
            ),

            // ── Stats Row ──
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 32, vertical: 20),
              child: Row(
                children: [
                  Expanded(child: _statPill('Level', level, AppTheme.lognity500, isDark)),
                  const SizedBox(width: 12),
                  Expanded(child: _statPill('Poin', '$points', Colors.amber.shade700, isDark)),
                ],
              ),
            ),

            // ── Badges ──
            if (badges.isNotEmpty) ...[
              _sectionHeader('Badges (${badges.length})', Icons.emoji_events_rounded, Colors.amber),
              const SizedBox(height: 12),
              SizedBox(
                height: 90,
                child: ListView.builder(
                  scrollDirection: Axis.horizontal,
                  padding: const EdgeInsets.symmetric(horizontal: 20),
                  itemCount: badges.length,
                  itemBuilder: (context, index) {
                    final badge = badges[index];
                    final rawColor = badge['raw_color'] ?? 'blue';
                    final badgeColor = _getBadgeColor(rawColor);
                    
                    return Container(
                      width: 80, margin: const EdgeInsets.only(right: 10),
                      decoration: BoxDecoration(
                        color: isDark ? badgeColor.withOpacity(0.2) : badgeColor.withOpacity(0.1),
                        borderRadius: BorderRadius.circular(14),
                        border: Border.all(color: isDark ? badgeColor.withOpacity(0.4) : badgeColor.withOpacity(0.3)),
                      ),
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Text(badge['icon'] ?? '🏆', style: const TextStyle(fontSize: 28)),
                          const SizedBox(height: 4),
                          Text(
                            badge['name'] ?? '',
                            textAlign: TextAlign.center,
                            style: GoogleFonts.poppins(fontSize: 10, fontWeight: FontWeight.w600, color: isDark ? badgeColor : badgeColor.withAlpha(255)),
                            maxLines: 2,
                          ),
                        ],
                      ),
                    );
                  },
                ),
              ),
              const SizedBox(height: 8),
            ],

            // ── Postingan ──
            _sectionHeader('Postingan (${recentRequests.length})', Icons.article_rounded, AppTheme.lognity500),
            const SizedBox(height: 8),
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 16),
              child: recentRequests.isEmpty
                  ? _emptyState('Belum ada postingan.')
                  : Column(
                      children: recentRequests.map((req) => _buildRequestCard(req, isDark)).toList(),
                    ),
            ),

            // ── Interaksi ──
            const SizedBox(height: 8),
            _sectionHeader('Interaksi Jawaban (${recentInteractions.length})', Icons.chat_rounded, AppTheme.funPurple),
            const SizedBox(height: 8),
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 16),
              child: recentInteractions.isEmpty
                  ? _emptyState('Belum ada interaksi.')
                  : Column(
                      children: recentInteractions.map((inter) => _buildInteractionCard(inter, isDark)).toList(),
                    ),
            ),
            const SizedBox(height: 32),
          ],
        ),
      ),
        ],
      ),
    );
  }

  Widget _statPill(String label, String value, Color color, bool isDark) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
      decoration: AppTheme.glassDecoration(isDark, borderRadius: 16),
      child: Column(
        children: [
          Text(label, style: GoogleFonts.poppins(fontSize: 11, color: Colors.grey.shade500, fontWeight: FontWeight.w500)),
          const SizedBox(height: 4),
          Text(value, style: GoogleFonts.poppins(fontSize: 18, fontWeight: FontWeight.bold, color: color)),
        ],
      ),
    );
  }

  Color _getBadgeColor(String rawColor) {
    switch (rawColor) {
      case 'red': return Colors.red;
      case 'green': return Colors.green;
      case 'yellow': return Colors.orange; // yellow in flutter is too bright
      case 'purple': return Colors.purple;
      case 'pink': return Colors.pink;
      case 'indigo': return Colors.indigo;
      case 'teal': return Colors.teal;
      case 'orange': return Colors.orange;
      case 'emerald': return Colors.greenAccent;
      case 'gray': return Colors.grey;
      case 'blue': 
      default: return Colors.blue;
    }
  }

  Widget _sectionHeader(String title, IconData icon, Color color) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 8),
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(6),
            decoration: BoxDecoration(color: color.withOpacity(0.1), borderRadius: BorderRadius.circular(8)),
            child: Icon(icon, size: 16, color: color),
          ),
          const SizedBox(width: 8),
          Text(title, style: GoogleFonts.poppins(fontSize: 16, fontWeight: FontWeight.bold)),
        ],
      ),
    );
  }

  Widget _emptyState(String text) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 16),
      child: Text(text, style: GoogleFonts.poppins(color: Colors.grey.shade400, fontSize: 13)),
    );
  }

  Widget _buildRequestCard(Map<String, dynamic> req, bool isDark) {
    return Container(
      margin: const EdgeInsets.only(bottom: 8),
      decoration: AppTheme.glassDecoration(isDark, borderRadius: 14),
      child: ListTile(
        contentPadding: const EdgeInsets.symmetric(horizontal: 14, vertical: 4),
        title: Text(
          req['description'] ?? 'Tanpa Deskripsi',
          maxLines: 2,
          overflow: TextOverflow.ellipsis,
          style: GoogleFonts.poppins(fontWeight: FontWeight.w600, fontSize: 13),
        ),
        subtitle: Container(
          margin: const EdgeInsets.only(top: 4),
          child: Row(
            children: [
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                decoration: BoxDecoration(color: AppTheme.lognity500.withOpacity(0.1), borderRadius: BorderRadius.circular(4)),
                child: Text(
                  req['category'] ?? '',
                  style: GoogleFonts.poppins(fontSize: 10, color: AppTheme.lognity600, fontWeight: FontWeight.w500),
                ),
              ),
            ],
          ),
        ),
        trailing: const Icon(Icons.chevron_right_rounded, size: 20),
        onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => ForumDetailScreen(forumId: req['request_id']))),
      ),
    );
  }

  Widget _buildInteractionCard(Map<String, dynamic> inter, bool isDark) {
    final reqDesc = inter['request'] != null ? inter['request']['description'] : 'Request Dihapus';
    return Container(
      margin: const EdgeInsets.only(bottom: 8),
      decoration: AppTheme.glassDecoration(isDark, borderRadius: 14),
      child: ListTile(
        contentPadding: const EdgeInsets.symmetric(horizontal: 14, vertical: 4),
        leading: Container(
          padding: const EdgeInsets.all(8),
          decoration: BoxDecoration(color: AppTheme.funPurple.withOpacity(0.1), borderRadius: BorderRadius.circular(10)),
          child: const Icon(Icons.chat_bubble_rounded, size: 18, color: AppTheme.funPurple),
        ),
        title: Text(
          inter['content'] ?? '',
          maxLines: 2,
          overflow: TextOverflow.ellipsis,
          style: GoogleFonts.poppins(fontSize: 13),
        ),
        subtitle: Text(
          'Di: $reqDesc',
          maxLines: 1,
          overflow: TextOverflow.ellipsis,
          style: GoogleFonts.poppins(color: Colors.grey.shade400, fontSize: 11),
        ),
        trailing: const Icon(Icons.chevron_right_rounded, size: 20),
        onTap: () {
          if (inter['request_id'] != null) {
            Navigator.push(context, MaterialPageRoute(builder: (_) => ForumDetailScreen(forumId: inter['request_id'])));
          }
        },
      ),
    );
  }
}
