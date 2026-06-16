import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'dart:ui';
import '../../services/api_service.dart';
import '../../theme/app_theme.dart';
import '../login_screen.dart';
import 'forum_detail_screen.dart';
import '../user_profile_screen.dart';
import 'create_forum_screen.dart';

class ForumIndexScreen extends StatefulWidget {
  @override
  _ForumIndexScreenState createState() => _ForumIndexScreenState();
}

class _ForumIndexScreenState extends State<ForumIndexScreen> {
  List<dynamic> forums = [];
  bool isLoading = true;
  bool _filterExpanded = false;
  bool _isGlobalFeed = true;

  TextEditingController _searchController = TextEditingController();
  String _selectedCategory = '';
  String _selectedFaculty = '';
  String _selectedSort = 'latest';

  int _page = 1;
  bool _hasMore = true;
  bool _isLoadingMore = false;
  ScrollController _scrollController = ScrollController();

  Map<String, String> _categoriesMap = {'': 'Semua'};
  Map<String, String> _facultiesMap = {'': 'Semua'};

  @override
  void initState() {
    super.initState();
    _fetchMetadata();
    _fetchForums();
    _scrollController.addListener(() {
      if (_scrollController.position.pixels >= _scrollController.position.maxScrollExtent - 100) {
        _loadMoreForums();
      }
    });
  }

  @override
  void dispose() {
    _scrollController.dispose();
    _searchController.dispose();
    super.dispose();
  }

  Future<void> _fetchMetadata() async {
    try {
      final data = await ApiService.getMetadata();
      setState(() {
        _categoriesMap = {'': 'Semua Kategori'};
        for (var c in data['categories']['forum'] ?? []) {
          _categoriesMap[c] = c;
        }

        _facultiesMap = {'': 'Semua Fakultas'};
        for (var f in data['faculties'] ?? []) {
          _facultiesMap[f] = f;
        }
      });
    } catch (e) {
      // print('Failed to fetch metadata');
    }
  }

  Future<void> _fetchForums({bool refresh = true}) async {
    if (refresh) {
      setState(() { isLoading = true; _page = 1; _hasMore = true; });
    }
    try {
      final data = await ApiService.getForums(
        page: _page, search: _searchController.text,
        category: _selectedCategory, faculty: _selectedFaculty, sort: _selectedSort,
        feed: _isGlobalFeed ? '' : 'following',
      );
      final List newForums = data['data'] ?? [];
      setState(() {
        if (refresh) { forums = newForums; } else { forums.addAll(newForums); }
        _hasMore = newForums.length >= 10;
      });
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Gagal memuat forum')));
    } finally {
      setState(() { isLoading = false; _isLoadingMore = false; });
    }
  }

  Future<void> _loadMoreForums() async {
    if (_hasMore && !_isLoadingMore && !isLoading) {
      setState(() => _isLoadingMore = true);
      _page++;
      await _fetchForums(refresh: false);
    }
  }

  Future<void> _toggleUpvote(int index, int forumId) async {
    try {
      final res = await ApiService.upvoteForum(forumId);
      setState(() {
        forums[index]['upvotes_count'] = res['upvotes_count'];
        forums[index]['is_upvoted'] = !(forums[index]['is_upvoted'] ?? false);
      });
    } catch (e) {
      AppTheme.showCustomErrorDialog(context, 'Gagal Upvote', e.toString().replaceAll('Exception: ', ''));
    }
  }

  Color _categoryColor(String? category) {
    switch (category) {
      case 'Catatan': return AppTheme.lognity500;
      case 'Tugas': return AppTheme.funPurple;
      case 'Jawaban UTS/UAS': return Colors.orange;
      case 'Diskusi': return AppTheme.funPink;
      case 'Lain-Lain': return Colors.grey;
      default: return AppTheme.lognity500;
    }
  }

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;

    return Scaffold(
      floatingActionButton: Container(
        decoration: BoxDecoration(
          gradient: AppTheme.primaryGradient,
          borderRadius: BorderRadius.circular(30),
          boxShadow: [BoxShadow(color: AppTheme.lognity600.withOpacity(0.4), blurRadius: 12, offset: const Offset(0, 4))],
        ),
        child: FloatingActionButton.extended(
          backgroundColor: Colors.transparent,
          elevation: 0,
          onPressed: () async {
            final result = await Navigator.push(context, MaterialPageRoute(builder: (_) => CreateForumScreen()));
            if (result == true) _fetchForums();
          },
          icon: const Icon(Icons.add_rounded, color: Colors.white, size: 24),
          label: Text('Diskusi Baru', style: GoogleFonts.poppins(color: Colors.white, fontWeight: FontWeight.w600)),
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
          Column(
        children: [
          // ── Search Bar + Filter Toggle ──
          Container(
            padding: const EdgeInsets.fromLTRB(16, 12, 16, 8),
            decoration: AppTheme.glassDecoration(isDark, borderRadius: 0).copyWith(
              border: null, // Remove border for this top container
              boxShadow: [BoxShadow(color: Colors.black.withOpacity(isDark ? 0.2 : 0.05), blurRadius: 8, offset: const Offset(0, 2))],
            ),
            child: Column(
              children: [
                // ── Global / Following Toggle ──
                Container(
                  margin: const EdgeInsets.only(bottom: 12),
                  padding: const EdgeInsets.all(4),
                  decoration: BoxDecoration(
                    color: isDark ? Colors.grey.shade800 : Colors.grey.shade100,
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: Row(
                    children: [
                      Expanded(
                        child: GestureDetector(
                          onTap: () {
                            if (!_isGlobalFeed) {
                              setState(() => _isGlobalFeed = true);
                              _fetchForums();
                            }
                          },
                          child: AnimatedContainer(
                            duration: const Duration(milliseconds: 200),
                            padding: const EdgeInsets.symmetric(vertical: 8),
                            decoration: BoxDecoration(
                              color: _isGlobalFeed ? (isDark ? Colors.grey.shade700 : Colors.white) : Colors.transparent,
                              borderRadius: BorderRadius.circular(10),
                              boxShadow: _isGlobalFeed ? [BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 4, offset: const Offset(0, 2))] : [],
                            ),
                            child: Center(
                              child: Text('Global', style: GoogleFonts.poppins(
                                fontWeight: _isGlobalFeed ? FontWeight.bold : FontWeight.w500,
                                color: _isGlobalFeed ? (isDark ? Colors.white : AppTheme.lognity600) : Colors.grey.shade500,
                                fontSize: 13,
                              )),
                            ),
                          ),
                        ),
                      ),
                      Expanded(
                        child: GestureDetector(
                          onTap: () {
                            if (_isGlobalFeed) {
                              setState(() => _isGlobalFeed = false);
                              _fetchForums();
                            }
                          },
                          child: AnimatedContainer(
                            duration: const Duration(milliseconds: 200),
                            padding: const EdgeInsets.symmetric(vertical: 8),
                            decoration: BoxDecoration(
                              color: !_isGlobalFeed ? (isDark ? Colors.grey.shade700 : Colors.white) : Colors.transparent,
                              borderRadius: BorderRadius.circular(10),
                              boxShadow: !_isGlobalFeed ? [BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 4, offset: const Offset(0, 2))] : [],
                            ),
                            child: Center(
                              child: Text('Mengikuti', style: GoogleFonts.poppins(
                                fontWeight: !_isGlobalFeed ? FontWeight.bold : FontWeight.w500,
                                color: !_isGlobalFeed ? (isDark ? Colors.white : AppTheme.funPurple) : Colors.grey.shade500,
                                fontSize: 13,
                              )),
                            ),
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
                Row(
                  children: [
                    Expanded(
                      child: TextField(
                        controller: _searchController,
                        decoration: InputDecoration(
                          hintText: 'Cari request atau matkul...',
                          hintStyle: GoogleFonts.poppins(fontSize: 13),
                          prefixIcon: const Icon(Icons.search_rounded, size: 20),
                          suffixIcon: _searchController.text.isNotEmpty ? IconButton(
                            icon: const Icon(Icons.clear_rounded, size: 18),
                            onPressed: () { _searchController.clear(); _fetchForums(); },
                          ) : null,
                          isDense: true,
                          contentPadding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                          filled: true,
                          fillColor: isDark ? Colors.grey.shade800 : Colors.grey.shade50,
                          border: OutlineInputBorder(borderRadius: BorderRadius.circular(14), borderSide: BorderSide.none),
                        ),
                        onSubmitted: (_) => _fetchForums(),
                      ),
                    ),
                    const SizedBox(width: 8),
                    GestureDetector(
                      onTap: () => setState(() => _filterExpanded = !_filterExpanded),
                      child: AnimatedContainer(
                        duration: const Duration(milliseconds: 200),
                        padding: const EdgeInsets.all(10),
                        decoration: BoxDecoration(
                          color: _filterExpanded
                              ? AppTheme.lognity500.withOpacity(0.1)
                              : (isDark ? Colors.grey.shade800 : Colors.grey.shade50),
                          borderRadius: BorderRadius.circular(14),
                          border: Border.all(color: _filterExpanded ? AppTheme.lognity500 : Colors.transparent),
                        ),
                        child: Icon(Icons.tune_rounded, size: 22,
                          color: _filterExpanded ? AppTheme.lognity500 : Colors.grey.shade600),
                      ),
                    ),
                  ],
                ),

                // ── Expandable Filter Panel ──
                AnimatedCrossFade(
                  firstChild: const SizedBox.shrink(),
                  secondChild: Padding(
                    padding: const EdgeInsets.only(top: 12),
                    child: Column(
                      children: [
                        Row(
                          children: [
                            Expanded(child: _buildDropdown('Kategori', _selectedCategory, _categoriesMap, (v) { setState(() => _selectedCategory = v); _fetchForums(); }, isDark)),
                            const SizedBox(width: 8),
                            Expanded(child: _buildDropdown('Fakultas', _selectedFaculty, _facultiesMap, (v) { setState(() => _selectedFaculty = v); _fetchForums(); }, isDark)),
                          ],
                        ),
                        const SizedBox(height: 8),
                        _buildDropdown('Urutkan', _selectedSort, {
                          'latest': 'Terbaru', 'popular': 'Terpopuler (Upvotes)', 'oldest': 'Terlama',
                        }, (v) { setState(() => _selectedSort = v); _fetchForums(); }, isDark),
                      ],
                    ),
                  ),
                  crossFadeState: _filterExpanded ? CrossFadeState.showSecond : CrossFadeState.showFirst,
                  duration: const Duration(milliseconds: 250),
                ),
              ],
            ),
          ),
          
          // ── Forum List ──
          Expanded(
            child: isLoading
                ? const Center(child: CircularProgressIndicator())
                : RefreshIndicator(
                    onRefresh: () => _fetchForums(),
                    child: forums.isEmpty
                        ? Center(child: Column(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              Icon(Icons.forum_outlined, size: 64, color: Colors.grey.shade300),
                              const SizedBox(height: 12),
                              Text('Belum ada request', style: GoogleFonts.poppins(color: Colors.grey.shade400)),
                            ],
                          ))
                        : ListView.builder(
                            controller: _scrollController,
                            padding: const EdgeInsets.fromLTRB(16, 8, 16, 80),
                            itemCount: forums.length + (_isLoadingMore ? 1 : 0),
                            itemBuilder: (context, index) {
                              if (index == forums.length) {
                                return const Padding(padding: EdgeInsets.symmetric(vertical: 16), child: Center(child: CircularProgressIndicator()));
                              }
                              return _buildForumCard(forums[index], index, isDark);
                            },
                          ),
                  ),
          ),
        ],
      ),
        ],
      ),
    );
  }

  Widget _buildForumCard(Map<String, dynamic> forum, int index, bool isDark) {
    final catColor = _categoryColor(forum['category']);
    
    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      decoration: AppTheme.glassDecoration(isDark, borderRadius: 16),
      child: InkWell(
        borderRadius: BorderRadius.circular(16),
        onTap: () async {
          final result = await Navigator.push(context, MaterialPageRoute(builder: (_) => ForumDetailScreen(forumId: forum['request_id'])));
          if (result == true) _fetchForums();
        },
        child: IntrinsicHeight(
          child: Row(
            children: [
              // Color accent bar
              Container(width: 4, decoration: BoxDecoration(
                color: catColor,
                borderRadius: const BorderRadius.only(topLeft: Radius.circular(16), bottomLeft: Radius.circular(16)),
              )),
              // Content
              Expanded(
                child: Padding(
                  padding: const EdgeInsets.all(14),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        children: [
                          CircleAvatar(
                            radius: 14,
                            backgroundColor: isDark ? Colors.grey.shade700 : Colors.grey.shade200,
                            backgroundImage: forum['user']?['profil_url'] != null
                                ? NetworkImage(ApiService.parseUrl(forum['user']!['profil_url'])) : null,
                            child: forum['user']?['profil_url'] == null ? Icon(Icons.person, size: 14, color: Colors.grey.shade500) : null,
                          ),
                          const SizedBox(width: 8),
                          Expanded(
                            child: Text(forum['user']?['username'] ?? 'User',
                              style: GoogleFonts.poppins(color: AppTheme.lognity600, fontWeight: FontWeight.w600, fontSize: 13),
                              maxLines: 1, overflow: TextOverflow.ellipsis),
                          ),
                          Container(
                            padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                            decoration: BoxDecoration(color: catColor.withOpacity(0.1), borderRadius: BorderRadius.circular(8)),
                            child: Text(forum['category'] ?? 'Umum', style: GoogleFonts.poppins(fontSize: 10, color: catColor, fontWeight: FontWeight.w600)),
                          ),
                        ],
                      ),
                      const SizedBox(height: 10),
                      Text(forum['description'] ?? '', maxLines: 2, overflow: TextOverflow.ellipsis,
                        style: GoogleFonts.poppins(fontWeight: FontWeight.w500, fontSize: 14, color: isDark ? Colors.white : Colors.grey.shade800)),
                      const SizedBox(height: 12),
                      Row(
                        children: [
                          GestureDetector(
                            onTap: () => _toggleUpvote(index, forum['request_id']),
                            child: AnimatedContainer(
                              duration: const Duration(milliseconds: 200),
                              padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
                              decoration: BoxDecoration(
                                color: (forum['is_upvoted'] == true)
                                    ? Colors.orange.shade50 : (isDark ? Colors.grey.shade800 : Colors.grey.shade50),
                                borderRadius: BorderRadius.circular(20),
                                border: Border.all(color: (forum['is_upvoted'] == true) ? Colors.orange.shade300 : Colors.transparent),
                              ),
                              child: Row(
                                children: [
                                  Icon(Icons.thumb_up_rounded, size: 14,
                                    color: (forum['is_upvoted'] == true) ? Colors.orangeAccent : Colors.grey.shade500),
                                  const SizedBox(width: 4),
                                  Text('${forum['upvotes_count'] ?? 0}', style: GoogleFonts.poppins(
                                    fontSize: 12, fontWeight: FontWeight.w600,
                                    color: (forum['is_upvoted'] == true) ? Colors.orange : Colors.grey.shade600)),
                                ],
                              ),
                            ),
                          ),
                          const SizedBox(width: 12),
                          Icon(Icons.chat_bubble_outline_rounded, size: 14, color: Colors.grey.shade500),
                          const SizedBox(width: 4),
                          Text('${forum['answers_count'] ?? 0}', style: GoogleFonts.poppins(fontSize: 12, color: Colors.grey.shade600)),
                        ],
                      ),
                    ],
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildDropdown(String label, String value, Map<String, String> items, Function(String) onChanged, bool isDark) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(label, style: GoogleFonts.poppins(fontSize: 11, fontWeight: FontWeight.w600, color: Colors.grey.shade500)),
        const SizedBox(height: 4),
        Container(
          padding: const EdgeInsets.symmetric(horizontal: 12),
          decoration: BoxDecoration(
            color: isDark ? Colors.grey.shade800 : Colors.grey.shade50,
            borderRadius: BorderRadius.circular(10),
            border: Border.all(color: isDark ? Colors.grey.shade700 : Colors.grey.shade300),
          ),
          child: DropdownButtonHideUnderline(
            child: DropdownButton<String>(
              value: value,
              isExpanded: true,
              icon: Icon(Icons.keyboard_arrow_down_rounded, size: 18, color: Colors.grey.shade500),
              items: items.entries.map((e) => DropdownMenuItem(value: e.key, child: Text(e.value, style: GoogleFonts.poppins(fontSize: 12)))).toList(),
              onChanged: (v) => onChanged(v ?? ''),
            ),
          ),
        ),
      ],
    );
  }
}
